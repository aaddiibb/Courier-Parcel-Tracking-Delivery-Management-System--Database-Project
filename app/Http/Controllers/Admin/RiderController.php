<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiderController extends Controller
{
    public function index()
    {
        $riders = DB::select(
            'SELECT r.rider_id, r.full_name, r.phone, r.vehicle_type, r.active_flag, b.branch_name
             FROM riders r
             LEFT JOIN branches b ON r.assigned_branch_id = b.branch_id
             ORDER BY r.full_name'
        );
        return view('admin.riders.index', compact('riders'));
    }

    public function create()
    {
        $branches = DB::select('SELECT branch_id, branch_name FROM branches ORDER BY branch_name');
        return view('admin.riders.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name'          => 'required|max:100',
            'phone'              => 'required|max:20',
            'vehicle_type'       => 'required|in:bicycle,motorcycle,van',
            'assigned_branch_id' => 'required|integer',
            'active_flag'        => 'required|in:Y,N',
        ]);

        $id = DB::select("SELECT seq_rider_id.NEXTVAL AS id FROM DUAL")[0]->id;

        DB::insert(
            'INSERT INTO riders (rider_id, full_name, phone, vehicle_type, assigned_branch_id, active_flag)
             VALUES (:id, :full_name, :phone, :vehicle_type, :assigned_branch_id, :active_flag)',
            [
                'id'                 => $id,
                'full_name'          => $request->full_name,
                'phone'              => $request->phone,
                'vehicle_type'       => $request->vehicle_type,
                'assigned_branch_id' => $request->assigned_branch_id,
                'active_flag'        => $request->active_flag,
            ]
        );

        return redirect()->route('admin.riders.index')->with('success', 'Rider created.');
    }

    public function show(string $id)
    {
        $rows = DB::select(
            'SELECT r.rider_id, r.full_name, r.phone, r.vehicle_type, r.active_flag, r.assigned_branch_id, b.branch_name
             FROM riders r
             LEFT JOIN branches b ON r.assigned_branch_id = b.branch_id
             WHERE r.rider_id = :id',
            ['id' => $id]
        );
        abort_if(empty($rows), 404);
        return view('admin.riders.show', ['rider' => $rows[0]]);
    }

    public function edit(string $id)
    {
        $rows = DB::select(
            'SELECT rider_id, full_name, phone, vehicle_type, assigned_branch_id, active_flag FROM riders WHERE rider_id = :id',
            ['id' => $id]
        );
        abort_if(empty($rows), 404);
        $branches = DB::select('SELECT branch_id, branch_name FROM branches ORDER BY branch_name');
        return view('admin.riders.edit', ['rider' => $rows[0], 'branches' => $branches]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'full_name'          => 'required|max:100',
            'phone'              => 'required|max:20',
            'vehicle_type'       => 'required|in:bicycle,motorcycle,van',
            'assigned_branch_id' => 'required|integer',
            'active_flag'        => 'required|in:Y,N',
        ]);

        DB::update(
            'UPDATE riders SET full_name = :full_name, phone = :phone, vehicle_type = :vehicle_type, assigned_branch_id = :assigned_branch_id, active_flag = :active_flag WHERE rider_id = :id',
            [
                'full_name'          => $request->full_name,
                'phone'              => $request->phone,
                'vehicle_type'       => $request->vehicle_type,
                'assigned_branch_id' => $request->assigned_branch_id,
                'active_flag'        => $request->active_flag,
                'id'                 => $id,
            ]
        );

        return redirect()->route('admin.riders.index')->with('success', 'Rider updated.');
    }

    public function destroy(string $id)
    {
        DB::delete('DELETE FROM riders WHERE rider_id = :id', ['id' => $id]);
        return redirect()->route('admin.riders.index')->with('success', 'Rider deleted.');
    }
}
