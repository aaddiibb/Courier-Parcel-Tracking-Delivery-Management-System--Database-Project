<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = DB::select('SELECT customer_id, full_name, phone, email, address FROM customers ORDER BY full_name');
        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|max:100',
            'phone'     => 'required|max:20',
            'email'     => 'nullable|email|max:100',
            'address'   => 'nullable|max:400',
        ]);

        $id = DB::select("SELECT seq_customer_id.NEXTVAL AS id FROM DUAL")[0]->id;

        DB::insert(
            'INSERT INTO customers (customer_id, full_name, phone, email, address, created_at)
             VALUES (:id, :full_name, :phone, :email, :address, SYSDATE)',
            [
                'id'        => $id,
                'full_name' => $request->full_name,
                'phone'     => $request->phone,
                'email'     => $request->email,
                'address'   => $request->address,
            ]
        );

        return redirect()->route('admin.customers.index')->with('success', 'Customer created.');
    }

    public function show(string $id)
    {
        $rows = DB::select(
            'SELECT customer_id, full_name, phone, email, address, created_at FROM customers WHERE customer_id = :id',
            ['id' => $id]
        );
        abort_if(empty($rows), 404);
        return view('admin.customers.show', ['customer' => $rows[0]]);
    }

    public function edit(string $id)
    {
        $rows = DB::select(
            'SELECT customer_id, full_name, phone, email, address FROM customers WHERE customer_id = :id',
            ['id' => $id]
        );
        abort_if(empty($rows), 404);
        return view('admin.customers.edit', ['customer' => $rows[0]]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'full_name' => 'required|max:100',
            'phone'     => 'required|max:20',
            'email'     => 'nullable|email|max:100',
            'address'   => 'nullable|max:400',
        ]);

        DB::update(
            'UPDATE customers SET full_name = :full_name, phone = :phone, email = :email, address = :address WHERE customer_id = :id',
            [
                'full_name' => $request->full_name,
                'phone'     => $request->phone,
                'email'     => $request->email,
                'address'   => $request->address,
                'id'        => $id,
            ]
        );

        return redirect()->route('admin.customers.index')->with('success', 'Customer updated.');
    }

    public function destroy(string $id)
    {
        DB::delete('DELETE FROM customers WHERE customer_id = :id', ['id' => $id]);
        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted.');
    }
}
