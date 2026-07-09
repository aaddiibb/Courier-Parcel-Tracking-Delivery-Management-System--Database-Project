-- 03-trg-auto-return.sql
-- Auto-returns a parcel after its 3rd failed delivery attempt.
--
-- The app-layer 3-strikes check in Rider\DeliveryController@logAttempt is
-- removed now that this trigger exists (see Phase C / docs/PROGRESS.md).
--
-- NOTE: a simple "AFTER INSERT ... FOR EACH ROW" version of this trigger
-- (querying delivery_attempts with a plain SELECT COUNT(*) inside the row
-- trigger body) was tried first and fails at runtime with ORA-04091 ("table
-- ... is mutating, trigger/function may not see it") on every failed
-- attempt — a row-level trigger cannot query the very table whose INSERT
-- fired it while that INSERT statement is still in progress, even though the
-- trigger and the table it's querying are the same table (which is exactly
-- the case here, not an exception to the rule). Fixed by using a COMPOUND
-- TRIGGER (supported since Oracle 11g): the AFTER EACH ROW section just
-- records which parcel_ids had a failed attempt in this statement; the
-- aggregate COUNT(*) and the UPDATE happen in the AFTER STATEMENT section,
-- by which point the INSERT has fully completed and the table is no longer
-- mutating.
--
-- Its UPDATE of parcels.current_status in turn fires trg_status_history
-- automatically — no manual parcel_status_history insert needed here.
--
-- Run as: cdb_admin@XE  Re-runnable (CREATE OR REPLACE).

CREATE OR REPLACE TRIGGER trg_auto_return
FOR INSERT ON delivery_attempts
COMPOUND TRIGGER

    TYPE t_parcel_ids IS TABLE OF delivery_attempts.parcel_id%TYPE INDEX BY PLS_INTEGER;
    v_parcel_ids t_parcel_ids;
    v_n          PLS_INTEGER := 0;

    AFTER EACH ROW IS
    BEGIN
        IF :NEW.success_flag = 'N' THEN
            v_n := v_n + 1;
            v_parcel_ids(v_n) := :NEW.parcel_id;
        END IF;
    END AFTER EACH ROW;

    AFTER STATEMENT IS
        v_fail_count NUMBER;
    BEGIN
        FOR i IN 1 .. v_n LOOP
            SELECT COUNT(*) INTO v_fail_count
            FROM delivery_attempts
            WHERE parcel_id = v_parcel_ids(i) AND success_flag = 'N';

            IF v_fail_count >= 3 THEN
                UPDATE parcels SET current_status = 'RETURNED'
                WHERE parcel_id = v_parcel_ids(i);
                -- trg_status_history fires automatically on the above UPDATE.
                -- No manual parcel_status_history insert needed here.
            END IF;
        END LOOP;
    END AFTER STATEMENT;

END trg_auto_return;
/

SHOW ERRORS TRIGGER trg_auto_return;
PROMPT Trigger TRG_AUTO_RETURN compiled.
