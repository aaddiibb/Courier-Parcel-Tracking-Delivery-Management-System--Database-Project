-- 01-block-structure.sql
-- Stored procedure: sp_parcel_cost_audit
-- Audits fee integrity for every active parcel using %TYPE, %ROWTYPE,
-- arithmetic operators (+, *, -), and comparison/logical operators.
-- Business rule: total_fee = base(50) + weight_kg * rate(20)
-- Run as: cdb_admin@XE
SET SERVEROUTPUT ON;

CREATE OR REPLACE PROCEDURE sp_parcel_cost_audit AS
    -- %TYPE-anchored local variables
    v_tracking  parcels.tracking_code%TYPE;
    v_weight    parcels.weight_kg%TYPE;
    v_sender    customers.full_name%TYPE;

    -- %ROWTYPE for the full fee record
    v_fee       fees%ROWTYPE;

    -- Arithmetic intermediates
    v_expected  NUMBER;
    v_diff      NUMBER;
    v_line_no   NUMBER := 0;

    CURSOR c IS
        SELECT p.parcel_id, p.tracking_code, p.weight_kg,
               c.full_name AS sender_name
        FROM   parcels p
        JOIN   customers c ON c.customer_id = p.sender_customer_id
        WHERE  p.current_status NOT IN ('DELIVERED', 'RETURNED')
        ORDER  BY p.tracking_code;
BEGIN
    DELETE FROM plsql_log WHERE block_id = 'COST-AUDIT';

    FOR rec IN c LOOP
        -- Assign %TYPE variables from cursor row
        v_tracking := rec.tracking_code;
        v_weight   := rec.weight_kg;
        v_sender   := rec.sender_name;

        BEGIN
            -- Fetch full fee row using %ROWTYPE
            SELECT * INTO v_fee FROM fees WHERE parcel_id = rec.parcel_id;

            -- Arithmetic: expected = base(50) + weight_kg * rate(20)
            v_expected := 50 + (v_weight * 20);
            v_diff     := v_fee.total_amount - v_expected;
            v_line_no  := v_line_no + 1;

            -- Comparison + logical operators to detect fee mismatch
            IF ABS(v_diff) > 0.01 AND v_fee.total_amount != v_expected THEN
                INSERT INTO plsql_log VALUES (
                    'COST-AUDIT', v_line_no,
                    'MISMATCH|' || v_tracking || '|' || v_sender || '|' ||
                    v_weight || '|' || v_expected || '|' ||
                    v_fee.total_amount || '|' || v_fee.paid_flag
                );
            ELSE
                INSERT INTO plsql_log VALUES (
                    'COST-AUDIT', v_line_no,
                    'OK|' || v_tracking || '|' || v_sender || '|' ||
                    v_weight || '|' || v_expected || '|' ||
                    v_fee.total_amount || '|' || v_fee.paid_flag
                );
            END IF;

        EXCEPTION
            WHEN NO_DATA_FOUND THEN
                v_line_no := v_line_no + 1;
                INSERT INTO plsql_log VALUES (
                    'COST-AUDIT', v_line_no,
                    'NO-FEE|' || v_tracking || '|' || v_sender || '|' ||
                    v_weight || '|0|0|N'
                );
        END;
    END LOOP;

    IF v_line_no = 0 THEN
        INSERT INTO plsql_log VALUES ('COST-AUDIT', 1, 'EMPTY||||0|0|N');
    END IF;

    COMMIT;
END sp_parcel_cost_audit;
/
