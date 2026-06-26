<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index()
    {
        // --- Operations Overview ---
        $statusBreakdown = DB::select(
            "SELECT current_status AS status, COUNT(*) AS total
             FROM parcels
             GROUP BY current_status
             ORDER BY total DESC"
        );

        $revenueSummary = DB::select(
            "SELECT
                SUM(CASE WHEN paid_flag = 'Y' THEN total_amount ELSE 0 END) AS paid_total,
                SUM(CASE WHEN paid_flag = 'N' THEN total_amount ELSE 0 END) AS unpaid_total,
                SUM(total_amount) AS grand_total
             FROM fees"
        );
        $revenue = $revenueSummary[0] ?? (object)['paid_total' => 0, 'unpaid_total' => 0, 'grand_total' => 0];

        // --- Branch Performance ---
        $branchPerformance = DB::select(
            "SELECT b.branch_name, b.city,
                    COUNT(p.parcel_id)              AS parcel_count,
                    NVL(SUM(f.total_amount), 0)     AS revenue,
                    ROUND(NVL(AVG(p.weight_kg), 0), 2) AS avg_weight_kg
             FROM branches b
             LEFT JOIN parcels p ON p.origin_branch_id = b.branch_id
             LEFT JOIN fees f    ON f.parcel_id = p.parcel_id
             GROUP BY b.branch_name, b.city
             ORDER BY revenue DESC"
        );

        $highVolumeBranches = DB::select(
            "SELECT b.branch_name, COUNT(*) AS parcel_count
             FROM parcels p
             JOIN branches b ON b.branch_id = p.origin_branch_id
             GROUP BY b.branch_name
             HAVING COUNT(*) > 4
             ORDER BY parcel_count DESC"
        );

        // --- Rider Performance ---
        $riderLeaderboard = DB::select(
            "SELECT r.full_name, b.branch_name,
                    COUNT(a.attempt_id) AS total_attempts,
                    SUM(CASE WHEN a.success_flag = 'Y' THEN 1 ELSE 0 END) AS successes,
                    CASE WHEN COUNT(a.attempt_id) > 0
                         THEN ROUND(
                              SUM(CASE WHEN a.success_flag = 'Y' THEN 1 ELSE 0 END) * 100
                              / COUNT(a.attempt_id), 1)
                         ELSE NULL END AS success_rate_pct
             FROM riders r
             LEFT JOIN branches b          ON b.branch_id = r.assigned_branch_id
             LEFT JOIN delivery_attempts a ON a.rider_id  = r.rider_id
             GROUP BY r.full_name, b.branch_name
             ORDER BY successes DESC, total_attempts DESC"
        );

        $underperformingRiders = DB::select(
            "SELECT r.full_name,
                    COUNT(*) AS total_attempts,
                    ROUND(
                        SUM(CASE WHEN a.success_flag = 'Y' THEN 1 ELSE 0 END) * 100 / COUNT(*),
                    1) AS success_rate_pct
             FROM delivery_attempts a
             JOIN riders r ON r.rider_id = a.rider_id
             GROUP BY r.full_name
             HAVING COUNT(*) > 2
                AND SUM(CASE WHEN a.success_flag = 'Y' THEN 1 ELSE 0 END) * 100 / COUNT(*) < 60
             ORDER BY success_rate_pct"
        );

        // --- Customer Activity ---
        $frequentCustomers = DB::select(
            "SELECT c.full_name, c.phone, COUNT(*) AS parcel_count
             FROM parcels p
             JOIN customers c ON c.customer_id = p.sender_customer_id
             GROUP BY c.full_name, c.phone
             HAVING COUNT(*) > 2
             ORDER BY parcel_count DESC"
        );

        $customersWithActiveShipments = DB::select(
            "SELECT c.full_name, c.phone
             FROM customers c
             WHERE EXISTS (
                 SELECT 1 FROM parcels p
                 WHERE p.sender_customer_id = c.customer_id
                   AND p.current_status = 'IN_TRANSIT'
             )
             ORDER BY c.full_name"
        );

        // --- Alerts ---
        $heavyParcels = DB::select(
            "SELECT p.tracking_code, p.weight_kg, p.current_status, c.full_name AS sender
             FROM parcels p
             JOIN customers c ON c.customer_id = p.sender_customer_id
             WHERE p.weight_kg > (SELECT AVG(weight_kg) FROM parcels)
             ORDER BY p.weight_kg DESC"
        );

        $inactiveBranches = DB::select(
            "SELECT b.branch_name, b.city
             FROM branches b
             WHERE NOT EXISTS (
                 SELECT 1 FROM parcels p WHERE p.origin_branch_id = b.branch_id
             )"
        );

        return view('reports.index', compact(
            'statusBreakdown',
            'revenue',
            'branchPerformance',
            'highVolumeBranches',
            'riderLeaderboard',
            'underperformingRiders',
            'frequentCustomers',
            'customersWithActiveShipments',
            'heavyParcels',
            'inactiveBranches'
        ));
    }

    public function operations()
    {
        // Execute the three stored procedures; each writes to plsql_log (GTT).
        // Results are read back in the same session before the connection closes.
        DB::statement('BEGIN sp_intransit_monitor; END;');
        DB::statement('BEGIN sp_weight_violation_scan; END;');
        DB::statement('BEGIN sp_parcel_cost_audit; END;');

        // --- Active Shipments Monitor (sp_intransit_monitor) ---
        $rawTransit = DB::select(
            "SELECT message FROM plsql_log
             WHERE  block_id = 'TRANSIT-MON'
             ORDER  BY line_no"
        );

        $transitSummary = null;
        $transitRows    = [];
        foreach ($rawTransit as $r) {
            if (str_starts_with($r->message, 'SUMMARY|')) {
                $parts          = explode('|', $r->message);
                $transitSummary = ['count' => $parts[1] ?? 0, 'total_kg' => $parts[2] ?? 0];
            } else {
                $p             = explode('|', $r->message);
                $transitRows[] = [
                    'tracking' => $p[0] ?? '',
                    'sender'   => $p[1] ?? '',
                    'origin'   => $p[2] ?? '',
                    'dest'     => $p[3] ?? '',
                    'weight'   => $p[4] ?? '',
                    'days'     => $p[5] ?? 0,
                ];
            }
        }

        // --- Weight Compliance Scan (sp_weight_violation_scan) ---
        $rawWeight  = DB::select(
            "SELECT message FROM plsql_log
             WHERE  block_id = 'WEIGHT-SCAN'
             ORDER  BY line_no"
        );

        $weightRows = [];
        $weightInfo = null;
        foreach ($rawWeight as $r) {
            if (str_starts_with($r->message, 'INFO|')) {
                $weightInfo = explode('|', $r->message)[1] ?? '';
            } else {
                $p            = explode('|', $r->message);
                $weightRows[] = [
                    'status'   => $p[0] ?? '',
                    'tracking' => $p[1] ?? '',
                    'weight'   => $p[2] ?? '',
                    'pstatus'  => $p[3] ?? '',
                    'sender'   => $p[4] ?? '',
                    'days'     => $p[5] ?? 0,
                ];
            }
        }

        // --- Fee Integrity Audit (sp_parcel_cost_audit) ---
        $rawCost  = DB::select(
            "SELECT message FROM plsql_log
             WHERE  block_id = 'COST-AUDIT'
             ORDER  BY line_no"
        );

        $costRows = [];
        foreach ($rawCost as $r) {
            if (str_starts_with($r->message, 'EMPTY|')) {
                continue;
            }
            $p          = explode('|', $r->message);
            $costRows[] = [
                'status'   => $p[0] ?? '',
                'tracking' => $p[1] ?? '',
                'sender'   => $p[2] ?? '',
                'weight'   => $p[3] ?? '',
                'expected' => $p[4] ?? '',
                'stored'   => $p[5] ?? '',
                'paid'     => $p[6] ?? '',
            ];
        }

        return view('reports.operations', compact(
            'transitRows',
            'transitSummary',
            'weightRows',
            'weightInfo',
            'costRows'
        ));
    }
}
