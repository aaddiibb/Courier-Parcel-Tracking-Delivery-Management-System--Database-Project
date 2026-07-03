<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const STATUSES = ['BOOKED', 'IN_TRANSIT', 'OUT_FOR_DELIVERY', 'DELIVERED', 'RETURNED'];

    public function index(): View
    {
        $branchId = auth()->user()->branch_id;

        $branchRows = DB::select(
            'SELECT branch_name, city FROM branches WHERE branch_id = :id',
            ['id' => $branchId]
        );
        $branchName = $branchRows[0]->branch_name ?? 'Unassigned Branch';

        $totalParcels = DB::select(
            'SELECT COUNT(*) AS cnt FROM parcels WHERE origin_branch_id = :id OR destination_branch_id = :id2',
            ['id' => $branchId, 'id2' => $branchId]
        )[0]->cnt;

        $todaysBookings = DB::select(
            "SELECT COUNT(*) AS cnt FROM parcels
             WHERE (origin_branch_id = :id OR destination_branch_id = :id2)
               AND TRUNC(booked_at) = TRUNC(SYSDATE)",
            ['id' => $branchId, 'id2' => $branchId]
        )[0]->cnt;

        $pendingDeliveries = DB::select(
            "SELECT COUNT(*) AS cnt FROM parcels
             WHERE (origin_branch_id = :id OR destination_branch_id = :id2)
               AND current_status = 'OUT_FOR_DELIVERY'",
            ['id' => $branchId, 'id2' => $branchId]
        )[0]->cnt;

        $statusCounts = DB::select(
            "SELECT current_status, COUNT(*) AS cnt FROM parcels
             WHERE origin_branch_id = :id OR destination_branch_id = :id2
             GROUP BY current_status",
            ['id' => $branchId, 'id2' => $branchId]
        );
        $countsByStatus = array_fill_keys(self::STATUSES, 0);
        foreach ($statusCounts as $row) {
            $countsByStatus[$row->current_status] = $row->cnt;
        }

        $recentParcels = DB::select(
            "SELECT * FROM (
                SELECT p.parcel_id, p.tracking_code, p.current_status, p.booked_at,
                       c.full_name AS sender_name,
                       b1.city AS origin_city, b2.city AS destination_city
                FROM parcels p
                JOIN customers c ON p.sender_customer_id = c.customer_id
                JOIN branches b1 ON p.origin_branch_id = b1.branch_id
                JOIN branches b2 ON p.destination_branch_id = b2.branch_id
                WHERE p.origin_branch_id = :id OR p.destination_branch_id = :id2
                ORDER BY p.booked_at DESC
             ) WHERE ROWNUM <= 15",
            ['id' => $branchId, 'id2' => $branchId]
        );

        return view('branch.dashboard', compact(
            'branchName', 'totalParcels', 'todaysBookings', 'pendingDeliveries',
            'countsByStatus', 'recentParcels'
        ) + ['statusOptions' => self::STATUSES]);
    }
}
