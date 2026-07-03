-- 02-assign-rider.sql
-- Assigns (or re-assigns) a rider to a parcel. A BOOKED parcel picking up its
-- first rider transitions to IN_TRANSIT; an already-IN_TRANSIT parcel just
-- gets a new rider with no status change. Any other current status is
-- rejected — you can't assign a rider to a parcel that's already out for
-- delivery, delivered, or returned.
--
-- NOTE: not wired into any controller yet (see docs/PROGRESS.md) — the admin
-- booking form's optional rider dropdown is not currently connected to this
-- procedure, a deliberate scope limit for today's pass.
--
-- NOTE: the BOOKED -> IN_TRANSIT branch relies on trg_status_history
-- (Prompt B) to log that transition; no manual history insert here.
--
-- Run as: cdb_admin@XE  Re-runnable (CREATE OR REPLACE).

CREATE OR REPLACE PROCEDURE assign_rider(
    p_parcel_id  IN NUMBER,
    p_rider_id   IN NUMBER,
    p_changed_by IN VARCHAR2
) IS
    v_status parcels.current_status%TYPE;
BEGIN
    SELECT current_status INTO v_status FROM parcels WHERE parcel_id = p_parcel_id;

    CASE v_status
        WHEN 'BOOKED' THEN
            UPDATE parcels
            SET assigned_rider_id = p_rider_id, current_status = 'IN_TRANSIT'
            WHERE parcel_id = p_parcel_id;
        WHEN 'IN_TRANSIT' THEN
            UPDATE parcels
            SET assigned_rider_id = p_rider_id
            WHERE parcel_id = p_parcel_id;
        ELSE
            RAISE_APPLICATION_ERROR(-20004, 'Cannot assign rider - parcel is already ' || v_status);
    END CASE;
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        RAISE_APPLICATION_ERROR(-20005, 'Parcel not found');
    WHEN OTHERS THEN
        RAISE;
END assign_rider;
/

SHOW ERRORS PROCEDURE assign_rider;
PROMPT Procedure ASSIGN_RIDER compiled.
