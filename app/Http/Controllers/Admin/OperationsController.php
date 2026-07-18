<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDO;
use PDOException;

class OperationsController extends Controller
{
    
    private function oracleErrorMessage(PDOException $e): string
    {
        if (preg_match('/ORA-\d+:\s*([^\n]+)/', $e->getMessage(), $m)) {
            return $m[1];
        }
        return 'A database error occurred.';
    }
    


    public function index()
    {
        $branches = DB::select('SELECT branch_id, branch_name FROM branches ORDER BY branch_name');

        $preview = DB::select(
            "SELECT b.branch_name, p.current_status,
                    COUNT(*) AS stuck_count,
                    ROUND(AVG(SYSDATE - p.booked_at), 1) AS avg_days_stuck
             FROM parcels p JOIN branches b ON p.origin_branch_id = b.branch_id
             WHERE p.current_status IN ('IN_TRANSIT', 'OUT_FOR_DELIVERY')
               AND (SYSDATE - p.booked_at) > 3
             GROUP BY b.branch_name, p.current_status
             ORDER BY avg_days_stuck DESC"
        );

        return view('admin.operations.index', compact('branches', 'preview'));
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'branch_id'      => 'required|integer',
            'days_threshold' => 'required|integer|min:1',
            'new_status'     => 'required|in:RETURNED,DELIVERED',
        ]);

        try {
            $pdo  = DB::getPdo();
            $stmt = $pdo->prepare('BEGIN bulk_update_stuck_parcels(:bid, :days, :st, :cnt); END;');
            $stmt->bindValue(':bid', $request->branch_id);
            $stmt->bindValue(':days', $request->days_threshold);
            $stmt->bindValue(':st', $request->new_status);
            $count = 0;
            $stmt->bindParam(':cnt', $count, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 10);
            $stmt->execute();
        } catch (PDOException $e) {
            return back()->withInput()->with('error', $this->oracleErrorMessage($e));
        }

        return redirect()->route('admin.operations')->with('success', "Successfully updated {$count} stuck parcels.");
    }
}
