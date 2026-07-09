<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(): View
    {
        $successRate = DB::select(
            "SELECT ROUND(SUM(CASE WHEN current_status = 'DELIVERED' THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 1) AS rate
             FROM parcels"
        )[0]->rate ?? 0;

        $avgDeliveryDays = DB::select(
            "SELECT ROUND(AVG(delivered_at - booked_at), 1) AS avg_days
             FROM parcels WHERE current_status = 'DELIVERED'"
        )[0]->avg_days ?? 0;

        $busiestBranchRows = DB::select(
            "SELECT branch_name FROM (
                SELECT b.branch_name, COUNT(*) cnt
                FROM branches b JOIN parcels p ON b.branch_id = p.origin_branch_id
                GROUP BY b.branch_name
                ORDER BY cnt DESC
             ) WHERE ROWNUM = 1"
        );
        $busiestBranch = $busiestBranchRows[0]->branch_name ?? '—';

        $topRiderRows = DB::select(
            "SELECT full_name, rate FROM (
                SELECT r.full_name, rider_success_rate(r.rider_id) AS rate,
                       (SELECT COUNT(*) FROM delivery_attempts WHERE rider_id = r.rider_id) AS attempts
                FROM riders r
             )
             WHERE attempts >= 5
             ORDER BY rate DESC"
        );
        $topRider = $topRiderRows[0]->full_name ?? 'No rider with 5+ attempts yet';
        $topRiderRate = $topRiderRows[0]->rate ?? null;

        return view('admin.analytics.index', compact(
            'successRate', 'avgDeliveryDays', 'busiestBranch', 'topRider', 'topRiderRate'
        ));
    }

    public function branches(): View
    {
        $branches = DB::select(
            "SELECT b.branch_name, b.city,
                    COUNT(p.parcel_id) AS total_parcels,
                    SUM(CASE WHEN p.current_status = 'DELIVERED' THEN 1 ELSE 0 END) AS delivered,
                    SUM(CASE WHEN p.current_status = 'RETURNED' THEN 1 ELSE 0 END) AS returned,
                    branch_revenue(b.branch_id) AS revenue,
                    ROUND(SUM(CASE WHEN p.current_status = 'DELIVERED' THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(p.parcel_id), 0), 1) AS success_pct
             FROM branches b LEFT JOIN parcels p ON p.origin_branch_id = b.branch_id
             GROUP BY b.branch_name, b.city, b.branch_id
             HAVING COUNT(p.parcel_id) > 0
             ORDER BY total_parcels DESC"
        );

        $underperformers = DB::select(
            "SELECT b.branch_name, COUNT(p.parcel_id) AS total
             FROM branches b LEFT JOIN parcels p ON p.origin_branch_id = b.branch_id
             GROUP BY b.branch_name
             HAVING COUNT(p.parcel_id) < (SELECT AVG(cnt) FROM (SELECT COUNT(*) cnt FROM parcels GROUP BY origin_branch_id))"
        );
        $underperformerNames = array_column($underperformers, 'branch_name');

        return view('admin.analytics.branches', compact('branches', 'underperformerNames'));
    }

    public function riders(): View
    {
        $riders = DB::select(
            "SELECT r.full_name, r.vehicle_type,
                    b.branch_name,
                    COUNT(da.attempt_id) AS total_attempts,
                    SUM(CASE WHEN da.success_flag = 'Y' THEN 1 ELSE 0 END) AS successful_cnt,
                    rider_success_rate(r.rider_id) AS success_rate,
                    COUNT(DISTINCT da.parcel_id) AS parcels_handled
             FROM riders r
             JOIN branches b ON r.assigned_branch_id = b.branch_id
             LEFT JOIN delivery_attempts da ON da.rider_id = r.rider_id
             GROUP BY r.rider_id, r.full_name, r.vehicle_type, b.branch_name
             ORDER BY success_rate DESC NULLS LAST"
        );

        $needsAttention = DB::select(
            "SELECT r.full_name, rider_success_rate(r.rider_id) AS rate
             FROM riders r
             WHERE rider_success_rate(r.rider_id) < 60
               AND (SELECT COUNT(*) FROM delivery_attempts WHERE rider_id = r.rider_id) >= 3"
        );

        return view('admin.analytics.riders', compact('riders', 'needsAttention'));
    }

    public function parcels(): View
    {
        $statusOrder = "CASE current_status WHEN 'BOOKED' THEN 1 WHEN 'IN_TRANSIT' THEN 2
                         WHEN 'OUT_FOR_DELIVERY' THEN 3 WHEN 'DELIVERED' THEN 4 ELSE 5 END";

        $funnel = DB::select(
            "SELECT current_status, COUNT(*) AS cnt,
                    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM parcels), 1) AS pct
             FROM parcels GROUP BY current_status ORDER BY {$statusOrder}"
        );

        $stuckParcels = DB::select(
            "SELECT p.tracking_code, c.full_name AS sender,
                    b_orig.city AS origin, b_dest.city AS destination,
                    TRUNC(SYSDATE - p.booked_at) AS days_in_system,
                    p.current_status
             FROM parcels p
             JOIN customers c ON p.sender_customer_id = c.customer_id
             JOIN branches b_orig ON p.origin_branch_id = b_orig.branch_id
             JOIN branches b_dest ON p.destination_branch_id = b_dest.branch_id
             WHERE p.current_status IN ('IN_TRANSIT', 'OUT_FOR_DELIVERY')
               AND (SYSDATE - p.booked_at) > 3
             ORDER BY days_in_system DESC"
        );

        $weightBands = DB::select(
            "SELECT
                CASE WHEN weight_kg <= 5 THEN '0-5 kg'
                     WHEN weight_kg <= 15 THEN '6-15 kg'
                     ELSE '16-50 kg' END AS weight_band,
                COUNT(*) AS cnt,
                ROUND(AVG(total_amount), 2) AS avg_fee
             FROM parcels p JOIN fees f ON p.parcel_id = f.parcel_id
             GROUP BY
                CASE WHEN weight_kg <= 5 THEN '0-5 kg'
                     WHEN weight_kg <= 15 THEN '6-15 kg'
                     ELSE '16-50 kg' END
             ORDER BY MIN(weight_kg)"
        );

        return view('admin.analytics.parcels', compact('funnel', 'stuckParcels', 'weightBands'));
    }
}
