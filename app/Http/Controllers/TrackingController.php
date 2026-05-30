<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackingController extends Controller
{
    public function index()
    {
        return view('public.track');
    }

    public function track(Request $request)
    {
        $request->validate([
            'tracking_code' => 'required|max:20',
        ]);

        $rows = DB::select("
            SELECT p.parcel_id, p.tracking_code, p.weight_kg, p.current_status, p.booked_at, p.delivered_at,
                   c.full_name AS sender_name,
                   rv.full_name AS receiver_name, rv.address AS receiver_address,
                   b1.branch_name AS origin_branch, b1.city AS origin_city,
                   b2.branch_name AS dest_branch, b2.city AS dest_city
            FROM parcels p
            JOIN customers c   ON p.sender_customer_id = c.customer_id
            JOIN receivers rv  ON p.receiver_id        = rv.receiver_id
            JOIN branches b1   ON p.origin_branch_id   = b1.branch_id
            JOIN branches b2   ON p.destination_branch_id = b2.branch_id
            WHERE UPPER(p.tracking_code) = UPPER(:code)
        ", ['code' => $request->tracking_code]);

        $parcel  = $rows[0] ?? null;
        $history = [];

        if ($parcel) {
            $history = DB::select("
                SELECT status, changed_at, changed_by, remarks
                FROM parcel_status_history
                WHERE parcel_id = :id
                ORDER BY changed_at DESC
            ", ['id' => $parcel->parcel_id]);
        }

        return view('public.track', compact('parcel', 'history'));
    }
}
