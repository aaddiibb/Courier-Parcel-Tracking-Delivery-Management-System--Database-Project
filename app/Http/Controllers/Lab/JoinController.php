<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class JoinController extends Controller
{
    public function index()
    {
        $demos = [];

        // (a) INNER JOIN
        $sql_a = "SELECT p.tracking_code, c.full_name AS sender, b.city AS destination, p.current_status
FROM parcels p
JOIN customers c ON p.sender_customer_id = c.customer_id
JOIN branches b ON p.destination_branch_id = b.branch_id";

        $demos[] = [
            'title'       => 'a) INNER JOIN — Parcels with sender and destination',
            'explanation' => 'Returns only rows that have a match in all joined tables. Parcels without a matching customer or destination branch are excluded. This is the most common join type.',
            'sql'         => $sql_a,
            'result'      => DB::select($sql_a),
        ];

        // (b) LEFT OUTER JOIN
        $sql_b = "SELECT b.branch_name, b.city, COUNT(p.parcel_id) AS parcel_count
FROM branches b
LEFT JOIN parcels p ON p.origin_branch_id = b.branch_id
GROUP BY b.branch_name, b.city
ORDER BY parcel_count DESC";

        $demos[] = [
            'title'       => 'b) LEFT OUTER JOIN — All branches with their outgoing parcel count',
            'explanation' => 'Returns every branch regardless of whether it has any parcels. Branches with zero outgoing parcels still appear with parcel_count = 0. Without LEFT JOIN, those branches would be silently dropped.',
            'sql'         => $sql_b,
            'result'      => DB::select($sql_b),
        ];

        // (c) RIGHT OUTER JOIN
        $sql_c = "SELECT r.full_name, COUNT(p.parcel_id) AS assigned_parcels
FROM parcels p
RIGHT JOIN riders r ON p.assigned_rider_id = r.rider_id
GROUP BY r.full_name";

        $demos[] = [
            'title'       => 'c) RIGHT OUTER JOIN — All riders even with no assigned parcels',
            'explanation' => 'The RIGHT side (riders) is the "keep-all" table. Every rider appears in the result even if no parcel references them. Useful for identifying idle or unassigned staff.',
            'sql'         => $sql_c,
            'result'      => DB::select($sql_c),
        ];

        // (d) FULL OUTER JOIN
        $sql_d = "SELECT b.branch_name, r.full_name
FROM branches b
FULL OUTER JOIN riders r ON r.assigned_branch_id = b.branch_id";

        $demos[] = [
            'title'       => 'd) FULL OUTER JOIN — Branches and riders, including unmatched rows on both sides',
            'explanation' => 'Combines LEFT and RIGHT outer joins. Riders without a branch assignment and branches with no riders both appear, with NULL on the unmatched side. Oracle supports FULL OUTER JOIN natively.',
            'sql'         => $sql_d,
            'result'      => DB::select($sql_d),
        ];

        // (e) CROSS JOIN (ROWNUM limit)
        $sql_e = "SELECT *
FROM (
    SELECT b.branch_name, r.full_name
    FROM branches b
    CROSS JOIN riders r
)
WHERE ROWNUM <= 10";

        $demos[] = [
            'title'       => 'e) CROSS JOIN — Cartesian product of branches × riders (limited to 10 rows)',
            'explanation' => 'Produces every possible (branch, rider) combination. With 6 branches and 12 riders the full result is 72 rows — ROWNUM limits the output for readability. CROSS JOIN is rarely useful in production (matrix pricing tables, test data generation); in most codebases it is a bug caused by a forgotten ON clause.',
            'sql'         => $sql_e,
            'result'      => DB::select($sql_e),
        ];

        // (f) NATURAL JOIN
        $sql_f = "SELECT *
FROM (SELECT parcel_id, current_status, weight_kg FROM parcels)
NATURAL JOIN (SELECT parcel_id, total_amount FROM fees)";

        $demos[] = [
            'title'       => 'f) NATURAL JOIN — Parcels joined to fees on matching column name (parcel_id)',
            'explanation' => 'NATURAL JOIN implicitly joins on all columns that share the same name in both sub-selects. Here both sides expose parcel_id so no ON clause is written. Risk: if a future column is added with the same name in both tables it silently becomes part of the join condition, potentially causing wrong results or no rows. Prefer explicit ON clauses in production code.',
            'sql'         => $sql_f,
            'result'      => DB::select($sql_f),
        ];

        // (g) SELF JOIN
        $sql_g = "SELECT r1.full_name AS rider, r2.full_name AS colleague
FROM riders r1
JOIN riders r2
  ON r1.assigned_branch_id = r2.assigned_branch_id
  AND r1.rider_id <> r2.rider_id
ORDER BY r1.full_name";

        $demos[] = [
            'title'       => 'g) SELF JOIN — Riders who share the same branch',
            'explanation' => 'Joins the riders table to itself using two aliases (r1, r2). The r1.rider_id <> r2.rider_id condition prevents pairing a rider with themselves. Classic use cases: employee–manager hierarchies, finding duplicates, and adjacency-list trees.',
            'sql'         => $sql_g,
            'result'      => DB::select($sql_g),
        ];

        // (h) Multi-column condition (Lab 10)
        $sql_h = "SELECT p.tracking_code, c.full_name AS sender, p.weight_kg
FROM parcels p
JOIN customers c ON p.sender_customer_id = c.customer_id
WHERE p.origin_branch_id <> p.destination_branch_id
  AND p.current_status = 'IN_TRANSIT'
  AND p.weight_kg > 5";

        $demos[] = [
            'title'       => 'h) Multi-column JOIN condition (Lab 10) — Heavy in-transit inter-branch parcels',
            'explanation' => 'Demonstrates filtering across joined tables with multiple conditions: cross-branch routing (origin ≠ destination), a specific status, and a weight threshold. Each condition independently reduces the result set; the query returns only parcels that satisfy all three simultaneously.',
            'sql'         => $sql_h,
            'result'      => DB::select($sql_h),
        ];

        return view('lab.joins', compact('demos'));
    }
}
