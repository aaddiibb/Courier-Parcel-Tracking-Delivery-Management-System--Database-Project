<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SubqueryController extends Controller
{
    public function index()
    {
        $demos = [];

        // (a) Non-correlated scalar — parcels heavier than overall average
        $sql_a = "SELECT tracking_code, weight_kg
FROM parcels
WHERE weight_kg > (SELECT AVG(weight_kg) FROM parcels)
ORDER BY weight_kg DESC";

        $demos[] = [
            'title'       => 'a) Non-correlated scalar subquery — Parcels heavier than the overall average',
            'explanation' => 'The inner query runs once and returns a single value (the average weight). The outer query compares each parcel\'s weight against that value. Non-correlated because the inner query does not reference any column from the outer query — it is evaluated independently then substituted.',
            'sql'         => $sql_a,
            'result'      => DB::select($sql_a),
        ];

        // (b) Non-correlated multi-row IN — riders at branches in cities with >1 branch
        $sql_b = "SELECT r.full_name, b.branch_name, b.city
FROM riders r
JOIN branches b ON b.branch_id = r.assigned_branch_id
WHERE r.assigned_branch_id IN (
    SELECT branch_id
    FROM branches
    WHERE city IN (
        SELECT city
        FROM branches
        GROUP BY city
        HAVING COUNT(*) > 1
    )
)
ORDER BY b.city, r.full_name";

        $demos[] = [
            'title'       => 'b) Non-correlated IN with nested subquery — Riders in cities that have multiple branches',
            'explanation' => 'Two nested non-correlated subqueries: the innermost finds cities with more than one branch; the middle layer fetches the branch IDs in those cities; the outer query keeps only riders assigned to those branches. Each inner query executes once and returns a set — IN tests membership in that set.',
            'sql'         => $sql_b,
            'result'      => DB::select($sql_b),
        ];

        // (c) Correlated EXISTS — customers with at least one IN_TRANSIT parcel
        $sql_c = "SELECT c.full_name, c.phone
FROM customers c
WHERE EXISTS (
    SELECT 1
    FROM parcels p
    WHERE p.sender_customer_id = c.customer_id
      AND p.current_status = 'IN_TRANSIT'
)
ORDER BY c.full_name";

        $demos[] = [
            'title'       => 'c) Correlated EXISTS — Customers with at least one IN_TRANSIT parcel',
            'explanation' => 'The subquery references c.customer_id from the outer query — making it correlated. Oracle re-evaluates the inner query for each outer row. EXISTS short-circuits as soon as one matching row is found, so SELECT 1 (rather than SELECT *) is conventional. Use EXISTS instead of IN when the inner result set could be large.',
            'sql'         => $sql_c,
            'result'      => DB::select($sql_c),
        ];

        // (d) Subquery in FROM (inline view / derived table)
        $sql_d = "SELECT branch_name, parcel_count
FROM (
    SELECT b.branch_name, COUNT(*) AS parcel_count
    FROM parcels p
    JOIN branches b ON b.branch_id = p.origin_branch_id
    GROUP BY b.branch_name
)
WHERE parcel_count > 3
ORDER BY parcel_count DESC";

        $demos[] = [
            'title'       => 'd) Inline view (subquery in FROM) — Branches ranked by outgoing parcel count',
            'explanation' => 'The subquery in the FROM clause acts as a virtual table (Oracle calls this an inline view). The outer query filters and sorts the aggregate result — something you cannot do with a plain HAVING unless you repeat the aggregate expression. This is a common pattern when you need to filter on a derived column alias.',
            'sql'         => $sql_d,
            'result'      => DB::select($sql_d),
        ];

        // (e) Scalar subquery in SELECT column list
        $sql_e = "SELECT r.full_name,
       (SELECT COUNT(*)
        FROM delivery_attempts a
        WHERE a.rider_id = r.rider_id) AS total_attempts
FROM riders r
ORDER BY total_attempts DESC";

        $demos[] = [
            'title'       => 'e) Scalar subquery in SELECT — Total delivery attempts per rider',
            'explanation' => 'A correlated scalar subquery appears in the SELECT list rather than the WHERE clause. For each rider row, the inner query counts their attempts. This is equivalent to a LEFT JOIN + GROUP BY but reads more naturally when you want to augment a primary table with a single aggregated value from a related table.',
            'sql'         => $sql_e,
            'result'      => DB::select($sql_e),
        ];

        // (f) NOT EXISTS — branches that have never sent a parcel
        $sql_f = "SELECT b.branch_name, b.city
FROM branches b
WHERE NOT EXISTS (
    SELECT 1
    FROM parcels p
    WHERE p.origin_branch_id = b.branch_id
)
ORDER BY b.branch_name";

        $demos[] = [
            'title'       => 'f) NOT EXISTS — Branches that have never sent a parcel',
            'explanation' => 'NOT EXISTS returns rows where the correlated subquery finds zero matches. It is the cleanest way to express "rows in A with no corresponding row in B" — semantically clearer than NOT IN (which can return no rows at all if the subquery result contains a NULL). Identifies idle or newly-opened branches.',
            'sql'         => $sql_f,
            'result'      => DB::select($sql_f),
        ];

        return view('lab.subqueries', compact('demos'));
    }
}
