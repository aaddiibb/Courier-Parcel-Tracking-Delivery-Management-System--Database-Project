<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDOException;

class ParcelController extends Controller
{
    private const STATUSES = ['BOOKED', 'IN_TRANSIT', 'OUT_FOR_DELIVERY', 'DELIVERED', 'RETURNED'];

    private function branchName(int $branchId): string
    {
        $rows = DB::select('SELECT branch_name FROM branches WHERE branch_id = :id', ['id' => $branchId]);
        return $rows[0]->branch_name ?? 'Unassigned Branch';
    }

    /**
     * Pull the RAISE_APPLICATION_ERROR message out of an Oracle PDOException
     * (the driver's message body is "Error Code : N\nError Message :
     * ORA-N: <text>\nORA-06512: at ...", so the first "ORA-N: " line is the
     * one actually raised by our PL/SQL, not the call-stack trace below it).
     */
    private function oracleErrorMessage(PDOException $e): string
    {
        if (preg_match('/ORA-\d+:\s*([^\n]+)/', $e->getMessage(), $m)) {
            return $m[1];
        }
        return 'A database error occurred.';
    }

    private const SORT_COLUMNS = [
        'booked_at'      => 'p.booked_at',
        'current_status' => 'p.current_status',
    ];

    public function index(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date',
        ]);

        $branchId = auth()->user()->branch_id;

        $status = $request->query('status');
        if ($status !== null && ! in_array($status, self::STATUSES, true)) {
            $status = null;
        }
        $search  = trim((string) $request->query('search', ''));
        $dateFrom = $request->query('date_from');
        $dateTo   = $request->query('date_to');

        $sortColumn = self::SORT_COLUMNS[$request->query('sort')] ?? 'p.booked_at';
        $sortDir    = strtoupper((string) $request->query('dir')) === 'ASC' ? 'ASC' : 'DESC';

        // Branch-ownership condition is the base of the WHERE clause and is
        // never removed by any filter below — a branch manager can only ever
        // narrow this set further, never see outside it.
        $sql = "
            SELECT p.parcel_id, p.tracking_code,
                   c.full_name AS sender_name,
                   b1.city AS origin_city,
                   b2.city AS destination_city,
                   p.current_status, p.booked_at,
                   r.full_name AS rider_name
            FROM parcels p
            JOIN customers c   ON p.sender_customer_id = c.customer_id
            JOIN branches b1   ON p.origin_branch_id   = b1.branch_id
            JOIN branches b2   ON p.destination_branch_id = b2.branch_id
            LEFT JOIN riders r ON p.assigned_rider_id  = r.rider_id
            WHERE (p.origin_branch_id = :branch_id OR p.destination_branch_id = :branch_id2)
        ";
        $bindings = ['branch_id' => $branchId, 'branch_id2' => $branchId];

        if ($status !== null) {
            $sql .= ' AND p.current_status = :status';
            $bindings['status'] = $status;
        }

        if ($search !== '') {
            $sql .= ' AND (UPPER(p.tracking_code) LIKE :search OR UPPER(c.full_name) LIKE :search2)';
            $bindings['search'] = '%'.strtoupper($search).'%';
            $bindings['search2'] = '%'.strtoupper($search).'%';
        }

        if ($dateFrom) {
            $sql .= " AND TRUNC(p.booked_at) >= TO_DATE(:date_from, 'YYYY-MM-DD')";
            $bindings['date_from'] = $dateFrom;
        }

        if ($dateTo) {
            $sql .= " AND TRUNC(p.booked_at) <= TO_DATE(:date_to, 'YYYY-MM-DD')";
            $bindings['date_to'] = $dateTo;
        }

        $sql .= " ORDER BY {$sortColumn} {$sortDir}";

        $parcels = DB::select($sql, $bindings);

        return view('branch.parcels.index', [
            'parcels' => $parcels,
            'status' => $status,
            'search' => $search,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'sort' => $request->query('sort', 'booked_at'),
            'dir' => strtolower($sortDir),
            'statusOptions' => self::STATUSES,
            'branchName' => $this->branchName($branchId),
        ]);
    }

    public function show(string $id)
    {
        $branchId = auth()->user()->branch_id;

        $rows = DB::select("
            SELECT p.parcel_id, p.tracking_code, p.weight_kg, p.current_status, p.booked_at, p.delivered_at,
                   p.origin_branch_id, p.destination_branch_id,
                   c.full_name AS sender_name, c.phone AS sender_phone,
                   rv.full_name AS receiver_name, rv.phone AS receiver_phone, rv.address AS receiver_address,
                   b1.branch_name AS origin_branch, b1.city AS origin_city,
                   b2.branch_name AS dest_branch, b2.city AS dest_city,
                   r.full_name AS rider_name
            FROM parcels p
            JOIN customers c   ON p.sender_customer_id = c.customer_id
            JOIN receivers rv  ON p.receiver_id        = rv.receiver_id
            JOIN branches b1   ON p.origin_branch_id   = b1.branch_id
            JOIN branches b2   ON p.destination_branch_id = b2.branch_id
            LEFT JOIN riders r ON p.assigned_rider_id  = r.rider_id
            WHERE p.parcel_id = :id
        ", ['id' => $id]);
        abort_if(empty($rows), 404);
        $parcel = $rows[0];

        abort_unless(
            $parcel->origin_branch_id == $branchId || $parcel->destination_branch_id == $branchId,
            403,
            'This parcel does not belong to your branch.'
        );

        $history = DB::select("
            SELECT status, changed_at, changed_by, remarks
            FROM parcel_status_history
            WHERE parcel_id = :id
            ORDER BY changed_at DESC
        ", ['id' => $id]);

        $attempts = DB::select("
            SELECT da.attempt_id, da.attempted_at, da.success_flag, da.failure_reason,
                   r.full_name AS rider_name
            FROM delivery_attempts da
            LEFT JOIN riders r ON da.rider_id = r.rider_id
            WHERE da.parcel_id = :id
            ORDER BY da.attempted_at
        ", ['id' => $id]);

        return view('branch.parcels.show', compact('parcel', 'history', 'attempts') + [
            'statuses' => self::STATUSES,
            'branchName' => $this->branchName($branchId),
        ]);
    }

    public function updateStatus(Request $request, string $id)
    {
        $branchId = auth()->user()->branch_id;

        $rows = DB::select(
            'SELECT origin_branch_id, destination_branch_id FROM parcels WHERE parcel_id = :id',
            ['id' => $id]
        );
        abort_if(empty($rows), 404);

        abort_unless(
            $rows[0]->origin_branch_id == $branchId || $rows[0]->destination_branch_id == $branchId,
            403,
            'This parcel does not belong to your branch.'
        );

        $request->validate([
            'new_status' => 'required|in:BOOKED,IN_TRANSIT,OUT_FOR_DELIVERY,DELIVERED,RETURNED',
            'remarks'    => 'nullable|max:200',
        ]);

        try {
            $pdo  = DB::getPdo();
            $stmt = $pdo->prepare('BEGIN update_parcel_status(:pid, :st, :by, :rm); END;');
            $stmt->bindValue(':pid', $id);
            $stmt->bindValue(':st', $request->new_status);
            $stmt->bindValue(':by', auth()->user()->name);
            $stmt->bindValue(':rm', $request->remarks ?? null);
            $stmt->execute();
        } catch (PDOException $e) {
            return back()->with('error', $this->oracleErrorMessage($e));
        }

        return redirect()->route('branch.parcels.show', $id)->with('success', 'Status updated.');
    }
}
