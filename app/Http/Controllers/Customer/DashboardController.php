<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Support\CustomerContext;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $customer = CustomerContext::current();

        $stats         = [];
        $recentParcels = [];
        $totalSpend    = 0;

        if ($customer) {
            $statRows = DB::select("
                SELECT current_status, COUNT(*) AS cnt
                FROM parcels
                WHERE sender_customer_id = :id
                GROUP BY current_status
            ", ['id' => $customer->customer_id]);

            foreach ($statRows as $row) {
                $stats[$row->current_status] = $row->cnt;
            }

            $totalSpend = DB::select(
                'SELECT customer_total_spend(:id) AS spend FROM DUAL',
                ['id' => $customer->customer_id]
            )[0]->spend ?? 0;

            $recentParcels = DB::select("
                SELECT * FROM (
                    SELECT p.parcel_id, p.tracking_code, p.current_status, p.booked_at, p.delivered_at,
                           b2.city AS destination_city
                    FROM parcels p
                    JOIN branches b2 ON p.destination_branch_id = b2.branch_id
                    WHERE p.sender_customer_id = :id
                    ORDER BY p.booked_at DESC
                ) WHERE ROWNUM <= 5
            ", ['id' => $customer->customer_id]);
        }

        return view('customer.dashboard', compact('customer', 'stats', 'recentParcels', 'totalSpend'));
    }
}
