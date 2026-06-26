-- 03-cursor-intro.sql
-- Stored procedure: sp_intransit_monitor
-- Explicit cursor (OPEN/FETCH/CLOSE) over IN_TRANSIT parcels.
-- Joins sender, origin branch, and destination branch; computes days in transit.
-- Uses cursor attributes %NOTFOUND and %ROWCOUNT.
-- Run as: cdb_admin@XE
SET SERVEROUTPUT ON;

CREATE OR REPLACE PROCEDURE sp_intransit_monitor AS
    CURSOR c IS
        SELECT p.tracking_code,
               c.full_name                  AS sender_name,
               ob.branch_name               AS origin_branch,
               db.branch_name               AS dest_branch,
               p.weight_kg,
               TRUNC(SYSDATE - p.booked_at) AS days_in_transit
        FROM   parcels p
        JOIN   customers c  ON c.customer_id  = p.sender_customer_id
        JOIN   branches ob  ON ob.branch_id   = p.origin_branch_id
        JOIN   branches db  ON db.branch_id   = p.destination_branch_id
        WHERE  p.current_status = 'IN_TRANSIT'
        ORDER  BY days_in_transit DESC, p.tracking_code;

    v_row         c%ROWTYPE;
    v_line_no     NUMBER := 0;
    v_total_kg    NUMBER := 0;
    v_total_count NUMBER;
BEGIN
    DELETE FROM plsql_log WHERE block_id = 'TRANSIT-MON';

    OPEN c;
    LOOP
        FETCH c INTO v_row;
        EXIT WHEN c%NOTFOUND;

        v_line_no  := v_line_no + 1;
        v_total_kg := v_total_kg + v_row.weight_kg;

        INSERT INTO plsql_log VALUES (
            'TRANSIT-MON', v_line_no,
            v_row.tracking_code  || '|' ||
            v_row.sender_name    || '|' ||
            v_row.origin_branch  || '|' ||
            v_row.dest_branch    || '|' ||
            v_row.weight_kg      || '|' ||
            v_row.days_in_transit
        );
    END LOOP;

    -- Store count via %ROWCOUNT before closing cursor
    v_total_count := c%ROWCOUNT;
    CLOSE c;

    -- Summary row
    v_line_no := v_line_no + 1;
    INSERT INTO plsql_log VALUES (
        'TRANSIT-MON', v_line_no,
        'SUMMARY|' || v_total_count || '|' || ROUND(v_total_kg, 2)
    );

    COMMIT;
END sp_intransit_monitor;
/
