
CREATE OR REPLACE PROCEDURE update_parcel_status(
    p_parcel_id  IN NUMBER,
    p_new_status IN VARCHAR2,
    p_changed_by IN VARCHAR2,
    p_remarks    IN VARCHAR2 DEFAULT NULL
) IS
    v_old parcels.current_status%TYPE;
BEGIN
    SELECT current_status INTO v_old FROM parcels WHERE parcel_id = p_parcel_id;

    IF v_old = 'BOOKED' THEN
        IF p_new_status NOT IN ('IN_TRANSIT', 'RETURNED') THEN
            RAISE_APPLICATION_ERROR(-20007, 'Invalid status: ' || p_new_status);
        END IF;
    ELSIF v_old = 'IN_TRANSIT' THEN
        IF p_new_status NOT IN ('OUT_FOR_DELIVERY', 'RETURNED') THEN
            RAISE_APPLICATION_ERROR(-20007, 'Invalid status: ' || p_new_status);
        END IF;
    ELSIF v_old = 'OUT_FOR_DELIVERY' THEN
        IF p_new_status NOT IN ('DELIVERED', 'RETURNED') THEN
            RAISE_APPLICATION_ERROR(-20007, 'Invalid status: ' || p_new_status);
        END IF;
    ELSIF v_old IN ('DELIVERED', 'RETURNED') THEN
        RAISE_APPLICATION_ERROR(-20006, 'Parcel is in a terminal state: ' || v_old);
    ELSE
        RAISE_APPLICATION_ERROR(-20007, 'Invalid status: ' || p_new_status);
    END IF;

    UPDATE parcels
    SET current_status = p_new_status,
        delivered_at = CASE WHEN p_new_status = 'DELIVERED' THEN SYSDATE ELSE delivered_at END
    WHERE parcel_id = p_parcel_id;
    -- History logging is handled by trg_status_history trigger (Prompt B)
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        RAISE_APPLICATION_ERROR(-20008, 'Parcel not found');
    WHEN OTHERS THEN
        RAISE;
END update_parcel_status;
/

SHOW ERRORS PROCEDURE update_parcel_status;
PROMPT Procedure UPDATE_PARCEL_STATUS compiled.
