<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDOException;

class DeliveryController extends Controller
{
    /**
     * Resolve the rider_id linked to the authenticated user, and confirm the
     * given parcel is actually assigned to them. Aborts 403 otherwise — same
     * ownership-check pattern as Branch/ParcelController's branch scoping.
     */
    private function resolveOwnedParcel(string $parcelId): object
    {
        $riderRows = DB::select(
            'SELECT rider_id FROM riders WHERE user_id = :user_id',
            ['user_id' => auth()->user()->id]
        );
        abort_if(empty($riderRows), 403, 'Your account is not linked to a rider profile.');
        $riderId = $riderRows[0]->rider_id;

        $rows = DB::select("
            SELECT p.parcel_id, p.tracking_code, p.current_status, p.assigned_rider_id,
                   rv.full_name AS receiver_name, rv.phone AS receiver_phone, rv.address AS receiver_address
            FROM parcels p
            JOIN receivers rv ON p.receiver_id = rv.receiver_id
            WHERE p.parcel_id = :id
        ", ['id' => $parcelId]);
        abort_if(empty($rows), 404);
        $parcel = $rows[0];

        abort_unless($parcel->assigned_rider_id == $riderId, 403, 'This parcel is not assigned to you.');

        $parcel->rider_id = $riderId;
        return $parcel;
    }

    public function logForm(string $parcelId)
    {
        $parcel = $this->resolveOwnedParcel($parcelId);

        return view('rider.delivery.log', compact('parcel'));
    }

    public function logAttempt(Request $request, string $parcelId)
    {
        $parcel = $this->resolveOwnedParcel($parcelId);

        $validated = $request->validate([
            'outcome'         => 'required|in:success,failed',
            'failure_reason'  => 'required_if:outcome,failed|nullable|max:200',
        ]);

        $successFlag = $validated['outcome'] === 'success' ? 'Y' : 'N';

        // Auto-return after 3 failures is handled by trg_auto_return. Status
        // logging is handled by trg_status_history. This method only records
        // the attempt, and — for a successful delivery — moves the parcel to
        // DELIVERED via update_parcel_status; everything else downstream of
        // that INSERT/UPDATE happens in the database, not here.
        try {
            DB::transaction(function () use ($parcel, $successFlag, $validated) {
                $attemptId = DB::select('SELECT seq_attempt_id.NEXTVAL AS id FROM DUAL')[0]->id;
                DB::insert(
                    "INSERT INTO delivery_attempts (attempt_id, parcel_id, rider_id, attempted_at, success_flag, failure_reason)
                     VALUES (:aid, :pid, :rid, SYSDATE, :flag, :reason)",
                    [
                        'aid'    => $attemptId,
                        'pid'    => $parcel->parcel_id,
                        'rid'    => $parcel->rider_id,
                        'flag'   => $successFlag,
                        'reason' => $successFlag === 'N' ? $validated['failure_reason'] : null,
                    ]
                );

                if ($successFlag === 'Y') {
                    $this->transitionStatus($parcel->parcel_id, 'DELIVERED', 'Delivered by rider');
                }
            });
        } catch (PDOException $e) {
            return back()->withErrors(['outcome' => $this->oracleErrorMessage($e)]);
        }

        return redirect()->route('rider.dashboard')->with('success', 'Delivery attempt logged.');
    }

    /**
     * Pull the RAISE_APPLICATION_ERROR message out of an Oracle PDOException
     * (the driver's message body is "Error Code : N\nError Message :
     * ORA-N: <text>\nORA-06512: at ...", so the first "ORA-N: " line is the
     * one actually raised by our PL/SQL, not the call-stack trace below it).
     */
    private function oracleErrorMessage(PDOException $e): string
    {
        if (preg_match('/ORA-\d+:\s*([^\n]+)/', $e->getMessage(), $m)) {
            return $m[1];
        }
        return 'A database error occurred.';
    }

    private function transitionStatus(string $parcelId, string $status, string $remarks): void
    {
        $pdo  = DB::getPdo();
        $stmt = $pdo->prepare('BEGIN update_parcel_status(:pid, :st, :by, :rm); END;');
        $stmt->bindValue(':pid', $parcelId);
        $stmt->bindValue(':st', $status);
        $stmt->bindValue(':by', auth()->user()->name);
        $stmt->bindValue(':rm', $remarks);
        $stmt->execute();
    }
}
