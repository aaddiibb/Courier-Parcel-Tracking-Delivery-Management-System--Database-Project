-- 01-trg-status-history.sql
-- Auto-logs every status change made to a parcel.
--
-- Fires on every UPDATE to current_status — including calls from the
-- update_parcel_status procedure AND from trg_auto_return (03-trg-auto-return.sql)
-- below, since that trigger's UPDATE also changes current_status.
--
-- Does NOT fire on the initial INSERT (BOOKED status) — AFTER UPDATE triggers
-- never fire on INSERT — which is why book_parcel (06-procedures/01-book-parcel.sql)
-- still inserts that first history row manually. Do not remove that manual insert.
--
-- Run as: cdb_admin@XE  Re-runnable (CREATE OR REPLACE).

CREATE OR REPLACE TRIGGER trg_status_history
AFTER UPDATE OF current_status ON parcels
FOR EACH ROW
WHEN (OLD.current_status != NEW.current_status)
BEGIN
    INSERT INTO parcel_status_history (history_id, parcel_id, status, changed_at, changed_by, remarks)
    VALUES (seq_history_id.NEXTVAL, :NEW.parcel_id, :NEW.current_status, SYSDATE, USER,
            'Status changed from ' || :OLD.current_status || ' to ' || :NEW.current_status);
END;
/

SHOW ERRORS TRIGGER trg_status_history;
PROMPT Trigger TRG_STATUS_HISTORY compiled.
