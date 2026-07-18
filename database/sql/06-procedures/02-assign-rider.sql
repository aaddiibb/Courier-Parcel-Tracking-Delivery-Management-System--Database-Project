
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

