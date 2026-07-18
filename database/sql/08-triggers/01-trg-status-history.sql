
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

