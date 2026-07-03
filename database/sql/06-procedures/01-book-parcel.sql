-- 01-book-parcel.sql
-- Books a new parcel: validates weight and branches, generates the tracking
-- code, inserts the parcel row and its initial BOOKED history row.
--
-- NOTE: the initial BOOKED history insert here is intentional and stays even
-- after Prompt B adds trg_status_history — that trigger only fires on UPDATE
-- of current_status, never on INSERT, so this INSERT-time row would never be
-- logged automatically. Not a duplicate.
--
-- NOTE: no fee row is inserted here. Prompt B's trg_auto_fee trigger (fired
-- on INSERT INTO parcels) is responsible for that. Until Prompt B lands,
-- booking a parcel will NOT create a fees row — see docs/PROGRESS.md.
--
-- Depends on: calculate_fee (07-functions/01-calculate-fee.sql) — not called
-- directly by this procedure, but must compile first per the load order in
-- run-all.sql, since Prompt B's trigger will call it during this same INSERT.
--
-- Run as: cdb_admin@XE  Re-runnable (CREATE OR REPLACE).

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
PROMPT Procedure BOOK_PARCEL compiled.
