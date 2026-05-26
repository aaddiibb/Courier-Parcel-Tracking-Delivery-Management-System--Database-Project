<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    public function index()
    {
        $branches = DB::select('SELECT branch_id, branch_name, city, phone, manager_name FROM branches ORDER BY branch_name');
        return view('admin.branches.index', compact('branches'));
    }

    public function create()
    {
        return view('admin.branches.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_name'  => 'required|max:100',
            'city'         => 'required|max:60',
            'address'      => 'nullable|max:200',
            'phone'        => 'nullable|max:20',
            'manager_name' => 'nullable|max:100',
        ]);

        $id = DB::select("SELECT seq_branch_id.NEXTVAL AS id FROM DUAL")[0]->id;

        DB::insert(
            'INSERT INTO branches (branch_id, branch_name, city, address, phone, manager_name)
             VALUES (:id, :branch_name, :city, :address, :phone, :manager_name)',
            [
                'id'           => $id,
                'branch_name'  => $request->branch_name,
                'city'         => $request->city,
                'address'      => $request->address,
                'phone'        => $request->phone,
                'manager_name' => $request->manager_name,
            ]
        );

        return redirect()->route('admin.branches.index')->with('success', 'Branch created.');
    }

    public function show(string $id)
    {
        $rows = DB::select(
            'SELECT branch_id, branch_name, city, address, phone, manager_name FROM branches WHERE branch_id = :id',
            ['id' => $id]
        );
        abort_if(empty($rows), 404);
        return view('admin.branches.show', ['branch' => $rows[0]]);
    }

    public function edit(string $id)
    {
        $rows = DB::select(
            'SELECT branch_id, branch_name, city, address, phone, manager_name FROM branches WHERE branch_id = :id',
            ['id' => $id]
        );
        abort_if(empty($rows), 404);
        return view('admin.branches.edit', ['branch' => $rows[0]]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'branch_name'  => 'required|max:100',
            'city'         => 'required|max:60',
            'address'      => 'nullable|max:200',
            'phone'        => 'nullable|max:20',
            'manager_name' => 'nullable|max:100',
        ]);

        DB::update(
            'UPDATE branches SET branch_name = :branch_name, city = :city, address = :address, phone = :phone, manager_name = :manager_name WHERE branch_id = :id',
            [
                'branch_name'  => $request->branch_name,
                'city'         => $request->city,
                'address'      => $request->address,
                'phone'        => $request->phone,
                'manager_name' => $request->manager_name,
                'id'           => $id,
            ]
        );

        return redirect()->route('admin.branches.index')->with('success', 'Branch updated.');
    }

    public function destroy(string $id)
    {
        DB::delete('DELETE FROM branches WHERE branch_id = :id', ['id' => $id]);
        return redirect()->route('admin.branches.index')->with('success', 'Branch deleted.');
    }
}
