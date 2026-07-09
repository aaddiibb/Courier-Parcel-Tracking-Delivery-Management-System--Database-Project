<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDO;
use PDOException;

class ParcelController extends Controller
{
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
        'tracking_code'  => 'p.tracking_code',
        'current_status' => 'p.current_status',
        'weight_kg'      => 'p.weight_kg',
        'booked_at'      => 'p.booked_at',
        'total_amount'   => 'f.total_amount',
    ];

    public function index(Request $request)
    {
        $request->validate([
            'weight_min' => 'nullable|numeric|min:0',
            'weight_max' => 'nullable|numeric|min:0',
            'date_from'  => 'nullable|date',
            'date_to'    => 'nullable|date',
        ]);

        $conditions = [];
        $bindings   = [];

        if ($request->filled('tracking')) {
            $conditions[] = 'UPPER(p.tracking_code) LIKE UPPER(:tracking)';
            $bindings['tracking'] = '%'.strtoupper($request->tracking).'%';
        }
        if ($request->filled('status')) {
            $conditions[] = 'p.current_status = :status';
            $bindings['status'] = $request->status;
        }
        if ($request->filled('origin_branch')) {
            $conditions[] = 'p.origin_branch_id = :origin_branch';
            $bindings['origin_branch'] = $request->origin_branch;
        }
        if ($request->filled('dest_branch')) {
            $conditions[] = 'p.destination_branch_id = :dest_branch';
            $bindings['dest_branch'] = $request->dest_branch;
        }
        if ($request->filled('rider_id')) {
            $conditions[] = 'p.assigned_rider_id = :rider_id';
            $bindings['rider_id'] = $request->rider_id;
        }
        if ($request->filled('weight_min')) {
            $conditions[] = 'p.weight_kg >= :weight_min';
            $bindings['weight_min'] = $request->weight_min;
        }
        if ($request->filled('weight_max')) {
            $conditions[] = 'p.weight_kg <= :weight_max';
            $bindings['weight_max'] = $request->weight_max;
        }
        if ($request->filled('date_from')) {
            $conditions[] = "TRUNC(p.booked_at) >= TO_DATE(:date_from, 'YYYY-MM-DD')";
            $bindings['date_from'] = $request->date_from;
        }
        if ($request->filled('date_to')) {
            $conditions[] = "TRUNC(p.booked_at) <= TO_DATE(:date_to, 'YYYY-MM-DD')";
            $bindings['date_to'] = $request->date_to;
        }

        $where = $conditions ? 'WHERE '.implode(' AND ', $conditions) : '';

        $sortColumn = self::SORT_COLUMNS[$request->query('sort')] ?? 'p.booked_at';
        $sortDir    = strtoupper($request->query('dir')) === 'ASC' ? 'ASC' : 'DESC';

        $perPage     = 10;
        $currentPage = max(1, (int) $request->query('page', 1));
        $offset      = ($currentPage - 1) * $perPage;
        $rowStart    = $offset;
        $rowEnd      = $offset + $perPage;

        $innerSql = "
            SELECT p.parcel_id, p.tracking_code, p.current_status, p.weight_kg, p.booked_at,
                   c.full_name AS sender_name, c.phone AS sender_phone,
                   r_recv.full_name AS receiver_name,
                   b_orig.branch_name AS origin_branch, b_orig.city AS origin_city,
                   b_dest.branch_name AS dest_branch, b_dest.city AS dest_city,
                   ri.full_name AS rider_name,
                   f.total_amount AS fee
            FROM parcels p
            JOIN customers c ON p.sender_customer_id = c.customer_id
            JOIN receivers r_recv ON p.receiver_id = r_recv.receiver_id
            JOIN branches b_orig ON p.origin_branch_id = b_orig.branch_id
            JOIN branches b_dest ON p.destination_branch_id = b_dest.branch_id
            LEFT JOIN riders ri ON p.assigned_rider_id = ri.rider_id
            LEFT JOIN fees f ON p.parcel_id = f.parcel_id
            {$where}
            ORDER BY {$sortColumn} {$sortDir}
        ";

        // Oracle 11g ROWNUM pagination pattern. Bind names avoid Oracle
        // reserved words: :start and :end are both invalid bind variable
        // names (ORA-01745) since START and END are reserved keywords.
        $paginatedSql = "
            SELECT * FROM (
                SELECT a.*, ROWNUM rnum FROM ({$innerSql}) a WHERE ROWNUM <= :row_end
            ) WHERE rnum > :row_start
        ";
        $parcels = DB::select($paginatedSql, $bindings + ['row_end' => $rowEnd, 'row_start' => $rowStart]);

        // None of the filterable columns come from a joined table, and every
        // join above is either an INNER join on a NOT NULL FK (so it can
        // never drop a row) or a LEFT join (which also can't drop rows) —
        // so the count can skip the joins entirely and still match exactly.
        $total = DB::select("SELECT COUNT(*) AS cnt FROM parcels p {$where}", $bindings)[0]->cnt;

        $branches = DB::select('SELECT branch_id, branch_name FROM branches ORDER BY branch_name');
        $riders   = DB::select('SELECT rider_id, full_name FROM riders ORDER BY full_name');

        $filters = $request->only([
            'tracking', 'status', 'origin_branch', 'dest_branch', 'rider_id',
            'weight_min', 'weight_max', 'date_from', 'date_to', 'sort', 'dir',
        ]);

        return view('admin.parcels.index', [
            'parcels'     => $parcels,
            'total'       => $total,
            'currentPage' => $currentPage,
            'perPage'     => $perPage,
            'filters'     => $filters,
            'branches'    => $branches,
            'riders'      => $riders,
        ]);
    }

    public function create()
    {
        $customers = DB::select('SELECT customer_id, full_name FROM customers ORDER BY full_name');
        $receivers = DB::select('SELECT receiver_id, full_name FROM receivers ORDER BY full_name');
        $branches  = DB::select('SELECT branch_id, branch_name, city FROM branches ORDER BY branch_name');
        $riders    = DB::select("SELECT rider_id, full_name FROM riders WHERE active_flag = 'Y' ORDER BY full_name");
        return view('admin.parcels.create', compact('customers', 'receivers', 'branches', 'riders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sender_customer_id'    => 'required|integer',
            'receiver_id'           => 'required|integer',
            'origin_branch_id'      => 'required|integer',
            'destination_branch_id' => 'required|integer|different:origin_branch_id',
            'weight_kg'             => 'required|numeric|min:0.1|max:50',
            'assigned_rider_id'     => 'nullable|integer',
        ]);

        // Fee created automatically by trg_auto_fee when book_parcel inserts the parcel row.
        try {
            $pdo  = DB::getPdo();
            $stmt = $pdo->prepare('BEGIN book_parcel(:sid, :rid, :oid, :did, :wt, :by, :tc); END;');
            $stmt->bindValue(':sid', $request->sender_customer_id);
            $stmt->bindValue(':rid', $request->receiver_id);
            $stmt->bindValue(':oid', $request->origin_branch_id);
            $stmt->bindValue(':did', $request->destination_branch_id);
            $stmt->bindValue(':wt', $request->weight_kg);
            $stmt->bindValue(':by', auth()->user()->name);
            $trackingCode = '';
            $stmt->bindParam(':tc', $trackingCode, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 30);
            $stmt->execute();
        } catch (PDOException $e) {
            return back()->withInput()->with('error', $this->oracleErrorMessage($e));
        }

        $id = DB::select(
            'SELECT parcel_id FROM parcels WHERE tracking_code = :tc',
            ['tc' => $trackingCode]
        )[0]->parcel_id;

        // book_parcel has no rider parameter, so an optional rider chosen on
        // the create form is applied as a second step via assign_rider. The
        // parcel is already booked at this point regardless of what happens
        // here, so a failed assignment (e.g. inactive rider -> ORA-20010)
        // flashes as a warning rather than blocking the redirect.
        if ($request->filled('assigned_rider_id')) {
            try {
                $pdo  = DB::getPdo();
                $stmt = $pdo->prepare('BEGIN assign_rider(:pid, :rid, :by); END;');
                $stmt->bindValue(':pid', $id);
                $stmt->bindValue(':rid', $request->assigned_rider_id);
                $stmt->bindValue(':by', auth()->user()->name);
                $stmt->execute();
            } catch (PDOException $e) {
                return redirect()->route('admin.parcels.show', $id)
                    ->with('success', 'Parcel booked successfully.')
                    ->with('error', 'Rider could not be assigned: '.$this->oracleErrorMessage($e));
            }
        }

        return redirect()->route('admin.parcels.show', $id)->with('success', 'Parcel booked successfully.');
    }

    public function show(string $id)
    {
        $rows = DB::select("
            SELECT p.parcel_id, p.tracking_code, p.weight_kg, p.current_status, p.booked_at, p.delivered_at,
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

        $feeRows = DB::select("
            SELECT base_amount, weight_charge, total_amount, paid_flag, paid_at
            FROM fees WHERE parcel_id = :id
        ", ['id' => $id]);
        $fee = $feeRows[0] ?? null;

        $statuses = ['BOOKED', 'IN_TRANSIT', 'OUT_FOR_DELIVERY', 'DELIVERED', 'RETURNED'];

        return view('admin.parcels.show', compact('parcel', 'history', 'attempts', 'fee', 'statuses'));
    }

    public function updateStatus(Request $request, string $id)
    {
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

        return redirect()->route('admin.parcels.show', $id)->with('success', 'Status updated.');
    }
}
