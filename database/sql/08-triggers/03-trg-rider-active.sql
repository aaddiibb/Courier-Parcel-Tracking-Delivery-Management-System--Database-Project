

CREATE OR REPLACE TRIGGER trg_rider_active
BEFORE INSERT OR UPDATE OF assigned_rider_id ON parcels
FOR EACH ROW
WHEN (NEW.assigned_rider_id IS NOT NULL)
DECLARE
    v_active CHAR(1);
BEGIN
    SELECT active_flag INTO v_active FROM riders WHERE rider_id = :NEW.assigned_rider_id;

    IF v_active != 'Y' THEN
        RAISE_APPLICATION_ERROR(-20010, 'Cannot assign an inactive rider (rider_id=' ||
            :NEW.assigned_rider_id || ')');
    END IF;
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        RAISE_APPLICATION_ERROR(-20011, 'Rider not found (rider_id=' || :NEW.assigned_rider_id || ')');
END;
/

SHOW ERRORS TRIGGER trg_rider_active;

