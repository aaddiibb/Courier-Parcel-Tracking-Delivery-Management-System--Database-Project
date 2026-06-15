-- 03-cursor-intro.sql
-- Lab 11: Explicit cursor over IN_TRANSIT parcels.
-- Bridges into Day 10 (stored procedures with cursors).
-- Run as: cdb_admin@XE
SET SERVEROUTPUT ON;

DECLARE
    CURSOR c IS
        SELECT tracking_code, weight_kg
        FROM   parcels
        WHERE  current_status = 'IN_TRANSIT'
        ORDER  BY tracking_code;
    v_row     c%ROWTYPE;
    v_line_no NUMBER := 0;
    v_total   NUMBER;
BEGIN
    DBMS_OUTPUT.PUT_LINE('=== Explicit Cursor: IN_TRANSIT Parcels ===');

    DELETE FROM plsql_log WHERE block_id = 'CUR-A';

    OPEN c;
    LOOP
        FETCH c INTO v_row;
        EXIT WHEN c%NOTFOUND;
        v_line_no := v_line_no + 1;
        DBMS_OUTPUT.PUT_LINE(v_row.tracking_code || '  |  ' || v_row.weight_kg || ' kg');
        INSERT INTO plsql_log VALUES (
            'CUR-A', v_line_no,
            v_row.tracking_code || '  |  ' || v_row.weight_kg || ' kg'
        );
    END LOOP;

    v_total   := c%ROWCOUNT;
    CLOSE c;

    DBMS_OUTPUT.PUT_LINE('--- Total IN_TRANSIT: ' || v_total || ' parcel(s)');
    v_line_no := v_line_no + 1;
    INSERT INTO plsql_log VALUES ('CUR-A', v_line_no, '--- Total IN_TRANSIT: ' || v_total || ' parcel(s)');

    COMMIT;
END;
/
