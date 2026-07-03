-- 02-get-parcel-status.sql
-- Looks up a parcel's current status by tracking code.
-- Called by the customer tracking check; not surfaced directly as a page.
-- Run as: cdb_admin@XE  Re-runnable (CREATE OR REPLACE).

CREATE OR REPLACE FUNCTION get_parcel_status(p_tracking_code IN VARCHAR2) RETURN VARCHAR2 IS
    v_status parcels.current_status%TYPE;
BEGIN
    SELECT current_status INTO v_status
    FROM parcels
    WHERE tracking_code = p_tracking_code;

    RETURN v_status;
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        RETURN 'NOT_FOUND';
END get_parcel_status;
/

SHOW ERRORS FUNCTION get_parcel_status;
PROMPT Function GET_PARCEL_STATUS compiled.
