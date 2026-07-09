-- 04-trg-rider-active.sql
-- Blocks assigning an inactive (or nonexistent) rider to a parcel at the DB
-- level — a protection that didn't exist before today, since no controller
-- currently validates rider activity before setting assigned_rider_id.
--
-- Run as: cdb_admin@XE  Re-runnable (CREATE OR REPLACE).

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
PROMPT Trigger TRG_RIDER_ACTIVE compiled.
