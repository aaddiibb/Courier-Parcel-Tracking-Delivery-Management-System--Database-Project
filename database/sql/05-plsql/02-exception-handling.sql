-- 02-exception-handling.sql
-- Stored procedure: sp_weight_violation_scan
-- Scans all active parcels for weight compliance using named (NO_DATA_FOUND)
-- and user-defined (weight_limit_exceeded) exception handling.
-- Business rule: parcel weight must not exceed 50 kg.
-- Run as: cdb_admin@XE
SET SERVEROUTPUT ON;

CREATE OR REPLACE PROCEDURE sp_weight_violation_scan AS
    -- User-defined exception for the 50 kg business limit
    weight_limit_exceeded EXCEPTION;

    WEIGHT_LIMIT CONSTANT NUMBER := 50;

    v_active_count NUMBER;
    v_line_no      NUMBER := 0;

    CURSOR c IS
        SELECT p.tracking_code,
               p.weight_kg,
               p.current_status,
               c.full_name                  AS sender_name,
               TRUNC(SYSDATE - p.booked_at) AS days_active
        FROM   parcels p
        JOIN   customers c ON c.customer_id = p.sender_customer_id
        WHERE  p.current_status NOT IN ('DELIVERED', 'RETURNED')
        ORDER  BY p.weight_kg DESC;
BEGIN
    DELETE FROM plsql_log WHERE block_id = 'WEIGHT-SCAN';

    -- NO_DATA_FOUND: gracefully handle an empty active-parcel set
    BEGIN
        SELECT COUNT(*) INTO v_active_count
        FROM   parcels
        WHERE  current_status NOT IN ('DELIVERED', 'RETURNED')
          AND  ROWNUM = 1;

        IF v_active_count = 0 THEN
            RAISE NO_DATA_FOUND;
        END IF;
    EXCEPTION
        WHEN NO_DATA_FOUND THEN
            INSERT INTO plsql_log VALUES (
                'WEIGHT-SCAN', 1,
                'INFO|No active parcels to scan.'
            );
            COMMIT;
            RETURN;
    END;

    FOR rec IN c LOOP
        BEGIN
            -- Raise user-defined exception for over-limit parcel
            IF rec.weight_kg > WEIGHT_LIMIT THEN
                RAISE weight_limit_exceeded;
            END IF;

            -- Compliant parcel
            v_line_no := v_line_no + 1;
            INSERT INTO plsql_log VALUES (
                'WEIGHT-SCAN', v_line_no,
                'OK|' || rec.tracking_code || '|' || rec.weight_kg || '|' ||
                rec.current_status || '|' || rec.sender_name || '|' || rec.days_active
            );

        EXCEPTION
            WHEN weight_limit_exceeded THEN
                v_line_no := v_line_no + 1;
                INSERT INTO plsql_log VALUES (
                    'WEIGHT-SCAN', v_line_no,
                    'VIOLATION|' || rec.tracking_code || '|' || rec.weight_kg || '|' ||
                    rec.current_status || '|' || rec.sender_name || '|' || rec.days_active
                );
        END;
    END LOOP;

    COMMIT;
END sp_weight_violation_scan;
/
