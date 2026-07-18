

CREATE OR REPLACE PROCEDURE book_parcel(
    p_sender_id       IN  NUMBER,
    p_receiver_id     IN  NUMBER,
    p_origin_id       IN  NUMBER,
    p_dest_id         IN  NUMBER,
    p_weight          IN  NUMBER,
    p_booked_by       IN  VARCHAR2,
    p_tracking_code   OUT VARCHAR2
) IS
    v_id   NUMBER;
    v_code VARCHAR2(30);
BEGIN
    IF p_weight <= 0 OR p_weight > 50 THEN
        RAISE_APPLICATION_ERROR(-20001, 'Weight must be between 0.1 and 50 kg');
    END IF;

    IF p_origin_id = p_dest_id THEN
        RAISE_APPLICATION_ERROR(-20002, 'Origin and destination branches must be different');
    END IF;

    SELECT seq_parcel_id.NEXTVAL INTO v_id FROM DUAL;

    v_code := 'CDB' || TO_CHAR(SYSDATE, 'YYYY') || LPAD(TO_CHAR(v_id), 5, '0');

    INSERT INTO parcels (
        parcel_id, tracking_code, sender_customer_id, receiver_id,
        origin_branch_id, destination_branch_id, weight_kg, current_status, booked_at
    ) VALUES (
        v_id, v_code, p_sender_id, p_receiver_id,
        p_origin_id, p_dest_id, p_weight, 'BOOKED', SYSDATE
    );

    INSERT INTO parcel_status_history (
        history_id, parcel_id, status, changed_at, changed_by, remarks
    ) VALUES (
        seq_history_id.NEXTVAL, v_id, 'BOOKED', SYSDATE, p_booked_by, 'Parcel booked'
    );

    p_tracking_code := v_code;
EXCEPTION
    WHEN DUP_VAL_ON_INDEX THEN
        RAISE_APPLICATION_ERROR(-20003, 'Duplicate tracking code - retry');
    WHEN OTHERS THEN
        RAISE;
END book_parcel;
/

SHOW ERRORS PROCEDURE book_parcel;

