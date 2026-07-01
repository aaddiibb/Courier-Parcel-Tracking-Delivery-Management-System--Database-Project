<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParcelController extends Controller
{
    public function index()
    {
        $parcels = DB::select("
            SELECT p.parcel_id, p.tracking_code,
                   c.full_name AS sender_name,
                   b2.city AS destination_city,
                   p.current_status, p.booked_at,
                   r.full_name AS rider_name
            FROM parcels p
            JOIN customers c   ON p.sender_customer_id = c.customer_id
            JOIN receivers rv  ON p.receiver_id        = rv.receiver_id
            JOIN branches b1   ON p.origin_branch_id   = b1.branch_id
            JOIN branches b2   ON p.destination_branch_id = b2.branch_id
            LEFT JOIN riders r ON p.assigned_rider_id  = r.rider_id
            ORDER BY p.booked_at DESC
        ");
        return view('admin.parcels.index', compact('parcels'));
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

        $id = DB::select("SELECT seq_parcel_id.NEXTVAL AS id FROM DUAL")[0]->id;

        DB::transaction(function () use ($request, $id) {
            $trackingCode = DB::select(
                "SELECT 'CDB' || TO_CHAR(SYSDATE,'YYYY') || LPAD(:id, 5, '0') AS code FROM DUAL",
                ['id' => $id]
            )[0]->code;

            DB::insert("
                INSERT INTO parcels
                    (parcel_id, tracking_code, sender_customer_id, receiver_id,
                     origin_branch_id, destination_branch_id, assigned_rider_id,
                     weight_kg, current_status, booked_at)
                VALUES
                    (:id, :tc, :sender, :receiver, :origin, :dest, :rider, :weight, 'BOOKED', SYSDATE)
            ", [
                'id'       => $id,
                'tc'       => $trackingCode,
                'sender'   => $request->sender_customer_id,
                'receiver' => $request->receiver_id,
                'origin'   => $request->origin_branch_id,
                'dest'     => $request->destination_branch_id,
                'rider'    => $request->assigned_rider_id,
                'weight'   => $request->weight_kg,
            ]);

            $histId = DB::select("SELECT seq_history_id.NEXTVAL AS id FROM DUAL")[0]->id;
            DB::insert("
                INSERT INTO parcel_status_history (history_id, parcel_id, status, changed_at, changed_by, remarks)
                VALUES (:hid, :pid, 'BOOKED', SYSDATE, :changed_by, 'Parcel booked')
            ", ['hid' => $histId, 'pid' => $id, 'changed_by' => auth()->user()->name]);

            $base         = 50;
            $weightCharge = round($request->weight_kg * 20, 2);
            $total        = $base + $weightCharge;
            $feeId        = DB::select("SELECT seq_fee_id.NEXTVAL AS id FROM DUAL")[0]->id;
            DB::insert("
                INSERT INTO fees (fee_id, parcel_id, base_amount, weight_charge, total_amount, paid_flag)
                VALUES (:fid, :pid, :base, :wc, :total, 'N')
            ", ['fid' => $feeId, 'pid' => $id, 'base' => $base, 'wc' => $weightCharge, 'total' => $total]);
        });

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

        DB::transaction(function () use ($request, $id) {
            if ($request->new_status === 'DELIVERED') {
                DB::update(
                    "UPDATE parcels SET current_status = :status, delivered_at = SYSDATE WHERE parcel_id = :id",
                    ['status' => $request->new_status, 'id' => $id]
                );
            } else {
                DB::update(
                    "UPDATE parcels SET current_status = :status WHERE parcel_id = :id",
                    ['status' => $request->new_status, 'id' => $id]
                );
            }

            $histId = DB::select("SELECT seq_history_id.NEXTVAL AS id FROM DUAL")[0]->id;
            DB::insert("
                INSERT INTO parcel_status_history (history_id, parcel_id, status, changed_at, changed_by, remarks)
                VALUES (:hid, :pid, :status, SYSDATE, :changed_by, :remarks)
            ", [
                'hid'        => $histId,
                'pid'        => $id,
                'status'     => $request->new_status,
                'changed_by' => auth()->user()->name,
                'remarks'    => $request->remarks,
            ]);
        });

        return redirect()->route('admin.parcels.show', $id)->with('success', 'Status updated.');
    }
}
