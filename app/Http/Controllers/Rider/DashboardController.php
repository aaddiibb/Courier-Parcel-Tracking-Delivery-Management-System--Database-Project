<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $mode = $request->query('view', 'active') === 'completed' ? 'completed' : 'active';

        $riderRows = DB::select(
            'SELECT rider_id, full_name FROM riders WHERE user_id = :user_id',
            ['user_id' => auth()->user()->id]
        );

        if (empty($riderRows)) {
            return view('rider.dashboard', [
                'riderLinked' => false,
                'mode' => $mode,
                'activeJobs' => [],
                'completedJobs' => [],
                'todaysCompleted' => 0,
                'todaysFailed' => 0,
            ]);
        }

        $riderId = $riderRows[0]->rider_id;

        $activeJobs = [];
        $completedJobs = [];

        if ($mode === 'active') {
            $activeJobs = DB::select("
                SELECT p.parcel_id, p.tracking_code, p.weight_kg, p.current_status,
                       rv.full_name AS receiver_name, rv.phone AS receiver_phone, rv.address AS receiver_address
                FROM parcels p
                JOIN receivers rv ON p.receiver_id = rv.receiver_id
                WHERE p.assigned_rider_id = :rider_id
                  AND p.current_status IN ('IN_TRANSIT', 'OUT_FOR_DELIVERY')
                ORDER BY p.booked_at
            ", ['rider_id' => $riderId]);
        } else {
            // delivered_at is only set for DELIVERED parcels; for RETURNED
            // ones fall back to when parcel_status_history last recorded
            // that status (trg_status_history's row for the current status).
            $completedJobs = DB::select("
                SELECT p.parcel_id, p.tracking_code, p.current_status,
                       rv.full_name AS receiver_name,
                       COALESCE(p.delivered_at, (
                           SELECT MAX(changed_at) FROM parcel_status_history
                           WHERE parcel_id = p.parcel_id AND status = p.current_status
                       )) AS outcome_at
                FROM parcels p
                JOIN receivers rv ON p.receiver_id = rv.receiver_id
                WHERE p.assigned_rider_id = :rider_id
                  AND p.current_status IN ('DELIVERED', 'RETURNED')
                ORDER BY outcome_at DESC
            ", ['rider_id' => $riderId]);
        }

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
            'mode' => $mode,
            'activeJobs' => $activeJobs,
            'completedJobs' => $completedJobs,
            'todaysCompleted' => $todaysCompleted,
            'todaysFailed' => $todaysFailed,
        ]);
    }
}
