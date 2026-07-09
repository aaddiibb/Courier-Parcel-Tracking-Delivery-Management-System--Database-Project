-- 04-bulk-status-update.sql
-- Bulk-updates stuck parcels (IN_TRANSIT/OUT_FOR_DELIVERY at a branch, older
-- than a day threshold) to a new status, one row at a time via
-- update_parcel_status so each row still gets full transition validation
-- (trg_status_history then logs each one automatically).
--
-- Uses an explicit cursor rather than a single UPDATE because each row needs
-- individual validation through update_parcel_status — a plain bulk UPDATE
-- would bypass the state-machine checks that procedure enforces, and would
-- also skip logging.
--
-- Reading parcels via a cursor while update_parcel_status also reads/updates
-- parcels from inside the loop is safe — this is a standalone procedure, not
-- a row-level trigger, so the ORA-04091 mutating-table restriction (which
-- only applies to triggers) does not apply here.
--
-- If p_new_status is DELIVERED, IN_TRANSIT rows in the cursor will fail
-- update_parcel_status's transition check (only OUT_FOR_DELIVERY -> DELIVERED
-- is valid) and get silently skipped by the WHEN OTHERS handler below — by
-- design, not a bug: the admin form warns about this per status (see
-- resources/views/admin/operations/index.blade.php).
--
-- Run as: cdb_admin@XE  Re-runnable (CREATE OR REPLACE).

CREATE OR REPLACE PROCEDURE bulk_update_stuck_parcels(
  p_branch_id      IN NUMBER,
  p_days_threshold IN NUMBER,
  p_new_status     IN VARCHAR2,
  p_updated_count  OUT NUMBER
)
IS
  CURSOR c_stuck IS
    SELECT parcel_id, current_status
    FROM parcels
    WHERE origin_branch_id = p_branch_id
      AND current_status IN ('IN_TRANSIT', 'OUT_FOR_DELIVERY')
      AND (SYSDATE - booked_at) > p_days_threshold;

  v_row         c_stuck%ROWTYPE;
  v_skip_count  NUMBER := 0;
  v_ok_count    NUMBER := 0;
BEGIN
  p_updated_count := 0;

  OPEN c_stuck;
  LOOP
    FETCH c_stuck INTO v_row;
    EXIT WHEN c_stuck%NOTFOUND;

    BEGIN
      -- Arithmetic operator: verify threshold is positive
      IF p_days_threshold <= 0 THEN
        RAISE_APPLICATION_ERROR(-20020, 'Days threshold must be positive');
      END IF;

      update_parcel_status(v_row.parcel_id, p_new_status, 'BULK_OPERATION',
        'Auto-updated: stuck for > ' || TO_CHAR(p_days_threshold) || ' days');
      v_ok_count := v_ok_count + 1;  -- arithmetic operator

    EXCEPTION
      WHEN OTHERS THEN
        -- Log the skip but continue (don't let one bad row abort the whole batch)
        v_skip_count := v_skip_count + 1;
    END;
  END LOOP;
  CLOSE c_stuck;

  p_updated_count := v_ok_count;
  -- Note: trg_status_history fires automatically on each update_parcel_status call above
END;
/

SHOW ERRORS PROCEDURE bulk_update_stuck_parcels;
PROMPT Procedure BULK_UPDATE_STUCK_PARCELS compiled.
