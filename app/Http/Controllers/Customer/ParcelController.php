<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Support\CustomerContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParcelController extends Controller
{
    /**
     * Replacement for the old public /track page: only reachable when
     * logged in, and delegates to show() so the same ownership check
     * (sender_customer_id must match the caller) applies here too.
     */
    public function track(Request $request)
    {
        $request->validate(['tracking_code' => 'required|max:20']);

        return redirect()->route('customer.parcels.show', $request->tracking_code);
    }

    public function index()
    {
        $customerId = CustomerContext::id();

        if (! $customerId) {
            return view('customer.parcels.index', ['parcels' => []]);
        }

        $parcels = DB::select("
            SELECT p.parcel_id, p.tracking_code, p.current_status, p.booked_at, p.delivered_at,
                   b2.city AS destination_city, b2.branch_name AS dest_branch,
                   rv.full_name AS receiver_name
            FROM parcels p
            JOIN branches b2 ON p.destination_branch_id = b2.branch_id
            JOIN receivers rv ON p.receiver_id = rv.receiver_id
            WHERE p.sender_customer_id = :id
            ORDER BY p.booked_at DESC
        ", ['id' => $customerId]);

        return view('customer.parcels.index', compact('parcels'));
    }

    public function create()
    {
        $customerId = CustomerContext::id();
        abort_if(! $customerId, 403, 'Your account is not linked to a customer record yet.');

        $receivers = DB::select(
            'SELECT receiver_id, full_name, phone, address FROM receivers WHERE booking_customer_id = :id ORDER BY full_name',
            ['id' => $customerId]
        );
        $branches = DB::select('SELECT branch_id, branch_name, city FROM branches ORDER BY branch_name');

        return view('customer.parcels.create', compact('receivers', 'branches'));
    }

    public function store(Request $request)
    {
        $customerId = CustomerContext::id();
        abort_if(! $customerId, 403, 'Your account is not linked to a customer record yet.');

        $request->validate([
            'receiver_id'           => 'required|integer',
            'origin_branch_id'      => 'required|integer',
            'destination_branch_id' => 'required|integer|different:origin_branch_id',
            'weight_kg'             => 'required|numeric|min:0.1|max:50',
        ]);

        // Ownership check: the chosen receiver must belong to this customer —
        // never trust the hidden/select value alone.
        $ownsReceiver = DB::select(
            'SELECT receiver_id FROM receivers WHERE receiver_id = :rid AND booking_customer_id = :cid',
            ['rid' => $request->receiver_id, 'cid' => $customerId]
        );
        abort_if(empty($ownsReceiver), 403, 'That receiver does not belong to your account.');

        $id = DB::select('SELECT seq_parcel_id.NEXTVAL AS id FROM DUAL')[0]->id;

        DB::transaction(function () use ($request, $id, $customerId) {
            $trackingCode = DB::select(
                "SELECT 'CDB' || TO_CHAR(SYSDATE,'YYYY') || LPAD(:id, 5, '0') AS code FROM DUAL",
                ['id' => $id]
            )[0]->code;

            DB::insert("
                INSERT INTO parcels
                    (parcel_id, tracking_code, sender_customer_id, receiver_id,
                     origin_branch_id, destination_branch_id,
                     weight_kg, current_status, booked_at)
                VALUES
                    (:id, :tc, :sender, :receiver, :origin, :dest, :weight, 'BOOKED', SYSDATE)
            ", [
                'id'       => $id,
                'tc'       => $trackingCode,
                'sender'   => $customerId,
                'receiver' => $request->receiver_id,
                'origin'   => $request->origin_branch_id,
                'dest'     => $request->destination_branch_id,
                'weight'   => $request->weight_kg,
            ]);

            $histId = DB::select('SELECT seq_history_id.NEXTVAL AS id FROM DUAL')[0]->id;
            DB::insert("
                INSERT INTO parcel_status_history (history_id, parcel_id, status, changed_at, changed_by, remarks)
                VALUES (:hid, :pid, 'BOOKED', SYSDATE, :changed_by, 'Booked online by customer')
            ", ['hid' => $histId, 'pid' => $id, 'changed_by' => auth()->user()->name]);

            $base         = 50;
            $weightCharge = round($request->weight_kg * 20, 2);
            $total        = $base + $weightCharge;
            $feeId        = DB::select('SELECT seq_fee_id.NEXTVAL AS id FROM DUAL')[0]->id;
            DB::insert("
                INSERT INTO fees (fee_id, parcel_id, base_amount, weight_charge, total_amount, paid_flag)
                VALUES (:fid, :pid, :base, :wc, :total, 'N')
            ", ['fid' => $feeId, 'pid' => $id, 'base' => $base, 'wc' => $weightCharge, 'total' => $total]);
        });

        return redirect()->route('customer.parcels.show', $id)->with('success', 'Parcel booked successfully.');
    }

    public function show(string $trackingCode)
    {
        $customerId = CustomerContext::id();

        $rows = DB::select("
            SELECT p.parcel_id, p.tracking_code, p.weight_kg, p.current_status, p.booked_at, p.delivered_at,
                   p.sender_customer_id,
                   c.full_name AS sender_name, c.phone AS sender_phone,
                   rv.full_name AS receiver_name, rv.phone AS receiver_phone, rv.address AS receiver_address,
                   b1.branch_name AS origin_branch, b1.city AS origin_city,
                   b2.branch_name AS dest_branch, b2.city AS dest_city
            FROM parcels p
            JOIN customers c   ON p.sender_customer_id = c.customer_id
            JOIN receivers rv  ON p.receiver_id        = rv.receiver_id
            JOIN branches b1   ON p.origin_branch_id   = b1.branch_id
            JOIN branches b2   ON p.destination_branch_id = b2.branch_id
            WHERE UPPER(p.tracking_code) = UPPER(:code)
        ", ['code' => $trackingCode]);

        abort_if(empty($rows), 404);
        $parcel = $rows[0];

        // Ownership check: role middleware only proves the caller is A customer,
        // not that they own THIS parcel.
        abort_if($parcel->sender_customer_id != $customerId, 403, 'You do not have access to this parcel.');

        $history = DB::select("
            SELECT status, changed_at, changed_by, remarks
            FROM parcel_status_history
            WHERE parcel_id = :id
            ORDER BY changed_at ASC
        ", ['id' => $parcel->parcel_id]);

        return view('customer.parcels.show', compact('parcel', 'history'));
    }
}
