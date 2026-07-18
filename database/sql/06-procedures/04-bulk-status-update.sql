
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
      
      IF p_days_threshold <= 0 THEN
        RAISE_APPLICATION_ERROR(-20020, 'Days threshold must be positive');
      END IF;

      update_parcel_status(v_row.parcel_id, p_new_status, 'BULK_OPERATION',
        'Auto-updated: stuck for > ' || TO_CHAR(p_days_threshold) || ' days');
      v_ok_count := v_ok_count + 1;  -- arithmetic operator

    EXCEPTION
      WHEN OTHERS THEN
    
        v_skip_count := v_skip_count + 1;
    END;
  END LOOP;
  CLOSE c_stuck;

  p_updated_count := v_ok_count;

END;
/

SHOW ERRORS PROCEDURE bulk_update_stuck_parcels;

