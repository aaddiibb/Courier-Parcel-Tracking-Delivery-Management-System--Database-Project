<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const STATUSES = ['BOOKED', 'IN_TRANSIT', 'OUT_FOR_DELIVERY', 'DELIVERED', 'RETURNED'];

    public function index(Request $request): View
    {
        $totalParcels = DB::select('SELECT COUNT(*) AS cnt FROM parcels')[0]->cnt;

        $inTransit = DB::select(
            "SELECT COUNT(*) AS cnt FROM parcels WHERE current_status = 'IN_TRANSIT'"
        )[0]->cnt;

        $todaysRevenue = DB::select(
            "SELECT NVL(SUM(total_amount), 0) AS total FROM fees
             WHERE paid_flag = 'Y' AND TRUNC(paid_at) = TRUNC(SYSDATE)"
        )[0]->total;

        $activeRiders = DB::select(
            "SELECT COUNT(*) AS cnt FROM riders WHERE active_flag = 'Y'"
        )[0]->cnt;

        // ── Parcels-by-status filter ────────────────────────────────────
        $selectedStatus = $request->query('status', 'BOOKED');
        if (! in_array($selectedStatus, self::STATUSES, true)) {
            $selectedStatus = 'BOOKED';
        }

        $statusCount = DB::select(
            'SELECT COUNT(*) AS cnt FROM parcels WHERE current_status = :status',
            ['status' => $selectedStatus]
        )[0]->cnt;

        // ── Parcels-by-date-range filter ────────────────────────────────
        $dateFrom = $request->query('date_from') ?: now()->subDays(7)->toDateString();
        $dateTo   = $request->query('date_to') ?: now()->toDateString();

        $recentParcels = DB::select("
            SELECT p.parcel_id, p.tracking_code, p.current_status, p.booked_at,
                   c.full_name AS sender_name, b.city AS destination_city
            FROM parcels p
            JOIN customers c ON p.sender_customer_id = c.customer_id
            JOIN branches b ON p.destination_branch_id = b.branch_id
            WHERE TRUNC(p.booked_at) BETWEEN TO_DATE(:date_from, 'YYYY-MM-DD') AND TO_DATE(:date_to, 'YYYY-MM-DD')
            ORDER BY p.booked_at DESC
        ", ['date_from' => $dateFrom, 'date_to' => $dateTo]);

        return view('admin.dashboard', compact(
            'totalParcels', 'inTransit', 'todaysRevenue', 'activeRiders',
            'selectedStatus', 'statusCount',
            'dateFrom', 'dateTo', 'recentParcels'
        ) + ['statusOptions' => self::STATUSES]);
    }
}
