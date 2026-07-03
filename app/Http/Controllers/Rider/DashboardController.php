<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $riderRows = DB::select(
            'SELECT rider_id, full_name FROM riders WHERE user_id = :user_id',
            ['user_id' => auth()->user()->id]
        );

        if (empty($riderRows)) {
            return view('rider.dashboard', [
                'riderLinked' => false,
                'activeJobs' => [],
                'todaysCompleted' => 0,
                'todaysFailed' => 0,
            ]);
        }

        $riderId = $riderRows[0]->rider_id;

        $activeJobs = DB::select("
            SELECT p.parcel_id, p.tracking_code, p.weight_kg, p.current_status,
                   rv.full_name AS receiver_name, rv.phone AS receiver_phone, rv.address AS receiver_address
            FROM parcels p
            JOIN receivers rv ON p.receiver_id = rv.receiver_id
            WHERE p.assigned_rider_id = :rider_id
              AND p.current_status IN ('IN_TRANSIT', 'OUT_FOR_DELIVERY')
            ORDER BY p.booked_at
        ", ['rider_id' => $riderId]);

        $todaysCompleted = DB::select(
            "SELECT COUNT(*) AS cnt FROM delivery_attempts
             WHERE rider_id = :rider_id AND success_flag = 'Y' AND TRUNC(attempted_at) = TRUNC(SYSDATE)",
            ['rider_id' => $riderId]
        )[0]->cnt;

        $todaysFailed = DB::select(
            "SELECT COUNT(*) AS cnt FROM delivery_attempts
             WHERE rider_id = :rider_id AND success_flag = 'N' AND TRUNC(attempted_at) = TRUNC(SYSDATE)",
            ['rider_id' => $riderId]
        )[0]->cnt;

        return view('rider.dashboard', [
            'riderLinked' => true,
            'riderName' => $riderRows[0]->full_name,
            'activeJobs' => $activeJobs,
            'todaysCompleted' => $todaysCompleted,
            'todaysFailed' => $todaysFailed,
        ]);
    }
}
