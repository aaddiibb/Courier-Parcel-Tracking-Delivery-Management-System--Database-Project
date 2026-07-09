<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Support\CustomerContext;
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

    
        $ownsReceiver = DB::select(
            'SELECT receiver_id FROM receivers WHERE receiver_id = :rid AND booking_customer_id = :cid',
            ['rid' => $request->receiver_id, 'cid' => $customerId]
        );
        abort_if(empty($ownsReceiver), 403, 'That receiver does not belong to your account.');

        // Fee created automatically by trg_auto_fee when book_parcel inserts the parcel row.
        try {
            $pdo  = DB::getPdo();
            $stmt = $pdo->prepare('BEGIN book_parcel(:sid, :rid, :oid, :did, :wt, :by, :tc); END;');
            $stmt->bindValue(':sid', $customerId);
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

        return redirect()->route('customer.parcels.show', $trackingCode)->with('success', 'Parcel booked successfully.');
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
