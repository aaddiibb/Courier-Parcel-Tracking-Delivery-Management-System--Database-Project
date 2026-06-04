<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AggregateController extends Controller
{
    public function index()
    {
        $demos = [];

        // (a) COUNT GROUP BY status
        $sql_a = "SELECT current_status, COUNT(*) AS cnt
FROM parcels
GROUP BY current_status
ORDER BY cnt DESC";

        $demos[] = [
            'title'       => 'a) COUNT + GROUP BY — Parcel count per status',
            'explanation' => 'Counts how many parcels are in each status. GROUP BY collapses all rows with the same current_status into a single output row; COUNT(*) tallies them. The result is sorted descending so the busiest status appears first.',
            'sql'         => $sql_a,
            'result'      => DB::select($sql_a),
        ];

        // (b) SUM revenue per branch
        $sql_b = "SELECT b.branch_name, SUM(f.total_amount) AS revenue
FROM fees f
JOIN parcels p ON p.parcel_id = f.parcel_id
JOIN branches b ON b.branch_id = p.origin_branch_id
GROUP BY b.branch_name
ORDER BY revenue DESC";

        $demos[] = [
            'title'       => 'b) SUM — Revenue generated per origin branch',
            'explanation' => 'Joins fees → parcels → branches to attribute each fee to the parcel\'s origin branch, then sums total_amount per branch. Demonstrates SUM over a multi-table join result.',
            'sql'         => $sql_b,
            'result'      => DB::select($sql_b),
        ];

        // (c) AVG weight per branch
        $sql_c = "SELECT b.branch_name, ROUND(AVG(p.weight_kg), 2) AS avg_weight_kg
FROM parcels p
JOIN branches b ON b.branch_id = p.origin_branch_id
GROUP BY b.branch_name
ORDER BY avg_weight_kg DESC";

        $demos[] = [
            'title'       => 'c) AVG — Average parcel weight per origin branch',
            'explanation' => 'Computes the mean weight of all parcels sent from each branch. ROUND(..., 2) trims floating-point noise to two decimal places. Branches sending heavier parcels appear at the top.',
            'sql'         => $sql_c,
            'result'      => DB::select($sql_c),
        ];

        // (d) MAX and MIN weight per rider (combined)
        $sql_d = "SELECT r.full_name,
       MAX(p.weight_kg) AS max_weight_kg,
       MIN(p.weight_kg) AS min_weight_kg
FROM parcels p
JOIN riders r ON r.rider_id = p.assigned_rider_id
GROUP BY r.full_name
ORDER BY max_weight_kg DESC";

        $demos[] = [
            'title'       => 'd) MAX + MIN — Heaviest and lightest parcel per rider (single query)',
            'explanation' => 'Combines MAX and MIN in a single GROUP BY query so you can see each rider\'s weight range in one row. Using two separate queries would be wasteful; a single aggregate scan is more efficient.',
            'sql'         => $sql_d,
            'result'      => DB::select($sql_d),
        ];

        // (e) Rider success rate via SUM(CASE WHEN)
        $sql_e = "SELECT r.full_name,
       COUNT(*) AS total_attempts,
       SUM(CASE WHEN a.success_flag = 'Y' THEN 1 ELSE 0 END) AS successes,
       ROUND(
           SUM(CASE WHEN a.success_flag = 'Y' THEN 1 ELSE 0 END) * 100 / COUNT(*),
           1
       ) AS success_rate_pct
FROM delivery_attempts a
JOIN riders r ON r.rider_id = a.rider_id
GROUP BY r.full_name
ORDER BY success_rate_pct DESC";

        $demos[] = [
            'title'       => 'e) SUM(CASE WHEN) — Rider delivery success rate',
            'explanation' => 'Uses a conditional aggregate: SUM(CASE WHEN success_flag=\'Y\' THEN 1 ELSE 0 END) counts only successful attempts. Dividing by COUNT(*) gives the success rate. This pattern is Oracle\'s idiomatic substitute for COUNTIF.',
            'sql'         => $sql_e,
            'result'      => DB::select($sql_e),
        ];

        // (f) Paid vs unpaid totals with SUM(CASE)
        $sql_f = "SELECT SUM(CASE WHEN paid_flag = 'Y' THEN total_amount ELSE 0 END) AS paid_total,
       SUM(CASE WHEN paid_flag = 'N' THEN total_amount ELSE 0 END) AS unpaid_total,
       SUM(total_amount) AS grand_total
FROM fees";

        $demos[] = [
            'title'       => 'f) Pivot with SUM(CASE) — Paid vs unpaid revenue totals',
            'explanation' => 'Pivots a flag column into two aggregate columns without a GROUP BY. Each SUM(CASE...) filters a subset of rows and sums only those, producing a single-row summary that is useful for financial dashboards.',
            'sql'         => $sql_f,
            'result'      => DB::select($sql_f),
        ];

        // (g) HAVING — branches with >5 parcels
        $sql_g = "SELECT b.branch_name, COUNT(*) AS parcel_count
FROM parcels p
JOIN branches b ON b.branch_id = p.origin_branch_id
GROUP BY b.branch_name
HAVING COUNT(*) > 5
ORDER BY parcel_count DESC";

        $demos[] = [
            'title'       => 'g) HAVING — Branches with more than 5 outgoing parcels',
            'explanation' => 'HAVING filters groups after aggregation, unlike WHERE which filters rows before aggregation. You cannot write WHERE COUNT(*) > 5 — HAVING is the only way to filter on an aggregate result.',
            'sql'         => $sql_g,
            'result'      => DB::select($sql_g),
        ];

        // (h) HAVING + multi-condition — riders with >2 attempts AND success rate <60%
        $sql_h = "SELECT r.full_name,
       COUNT(*) AS total_attempts,
       ROUND(
           SUM(CASE WHEN a.success_flag = 'Y' THEN 1 ELSE 0 END) * 100 / COUNT(*),
           1
       ) AS success_rate_pct
FROM delivery_attempts a
JOIN riders r ON r.rider_id = a.rider_id
GROUP BY r.full_name
HAVING COUNT(*) > 2
   AND SUM(CASE WHEN a.success_flag = 'Y' THEN 1 ELSE 0 END) * 100 / COUNT(*) < 60
ORDER BY success_rate_pct";

        $demos[] = [
            'title'       => 'h) HAVING multi-condition — Riders with >2 attempts AND success rate below 60%',
            'explanation' => 'Two HAVING conditions combined with AND: the first filters on a plain count, the second on a conditional aggregate. Both must hold simultaneously. This pattern identifies underperforming riders who have had enough attempts to make the metric statistically meaningful.',
            'sql'         => $sql_h,
            'result'      => DB::select($sql_h),
        ];

        // (i) HAVING — customers with >2 bookings
        $sql_i = "SELECT c.full_name, COUNT(*) AS parcel_count
FROM parcels p
JOIN customers c ON c.customer_id = p.sender_customer_id
GROUP BY c.full_name
HAVING COUNT(*) > 2
ORDER BY parcel_count DESC";

        $demos[] = [
            'title'       => 'i) HAVING — Customers who have booked more than 2 parcels',
            'explanation' => 'Groups parcels by sender and keeps only high-volume customers. Useful for loyalty analytics: identifying repeat customers who might qualify for a discount tier.',
            'sql'         => $sql_i,
            'result'      => DB::select($sql_i),
        ];

        // (j) HAVING — statuses appearing >5 times in history
        $sql_j = "SELECT new_status, COUNT(*) AS occurrences
FROM parcel_status_history
GROUP BY new_status
HAVING COUNT(*) > 5
ORDER BY occurrences DESC";

        $demos[] = [
            'title'       => 'j) HAVING — Status transitions recorded more than 5 times in history',
            'explanation' => 'Counts how often each status value appears in the audit history table, then keeps only the frequent ones. Reveals which lifecycle stages are most common — a status with very few entries may indicate a bottleneck or data quality problem.',
            'sql'         => $sql_j,
            'result'      => DB::select($sql_j),
        ];

        return view('lab.aggregates', compact('demos'));
    }
}
