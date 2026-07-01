<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Support\CustomerContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceiverController extends Controller
{
    public function index()
    {
        $customerId = CustomerContext::id();

        $receivers = $customerId ? DB::select(
            'SELECT receiver_id, full_name, phone, address FROM receivers WHERE booking_customer_id = :id ORDER BY full_name',
            ['id' => $customerId]
        ) : [];

        return view('customer.receivers.index', compact('receivers'));
    }

    public function create()
    {
        abort_if(! CustomerContext::id(), 403, 'Your account is not linked to a customer record yet.');

        return view('customer.receivers.create');
    }

    public function store(Request $request)
    {
        $customerId = CustomerContext::id();
        abort_if(! $customerId, 403, 'Your account is not linked to a customer record yet.');

        $request->validate([
            'full_name' => 'required|max:100',
            'phone'     => 'required|max:20',
            'address'   => 'nullable|max:300',
        ]);

        $id = DB::select('SELECT seq_receiver_id.NEXTVAL AS id FROM DUAL')[0]->id;

        DB::insert("
            INSERT INTO receivers (receiver_id, full_name, phone, address, booking_customer_id)
            VALUES (:id, :name, :phone, :address, :cid)
        ", [
            'id'      => $id,
            'name'    => $request->full_name,
            'phone'   => $request->phone,
            'address' => $request->address,
            'cid'     => $customerId,
        ]);

        return redirect()->route('customer.receivers.index')->with('success', 'Receiver added.');
    }
}
