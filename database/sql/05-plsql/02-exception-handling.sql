-- 02-exception-handling.sql
-- Lab 11: Named and user-defined exception handling.
-- Run as: cdb_admin@XE
SET SERVEROUTPUT ON;

-- ============================================================
-- Block A: NO_DATA_FOUND — SELECT INTO returns zero rows.
-- ============================================================
DECLARE
    v_name customers.full_name%TYPE;
BEGIN
    SELECT full_name INTO v_name
    FROM   customers
    WHERE  customer_id = -999;  -- no such customer

    DBMS_OUTPUT.PUT_LINE('Found: ' || v_name);  -- never reached

    DELETE FROM plsql_log WHERE block_id = 'EX-A';
    INSERT INTO plsql_log VALUES ('EX-A', 1, 'Found: ' || v_name);
    COMMIT;

EXCEPTION
    WHEN NO_DATA_FOUND THEN
        DBMS_OUTPUT.PUT_LINE('NO_DATA_FOUND: no customer with customer_id = -999');
        DELETE FROM plsql_log WHERE block_id = 'EX-A';
        INSERT INTO plsql_log VALUES ('EX-A', 1, 'NO_DATA_FOUND: no customer with customer_id = -999');
        COMMIT;
END;
/

-- ============================================================
-- Block B: TOO_MANY_ROWS — SELECT INTO returns > 1 row.
-- ============================================================
DECLARE
    v_name customers.full_name%TYPE;
BEGIN
    -- No WHERE clause → multiple rows → TOO_MANY_ROWS
    SELECT full_name INTO v_name FROM customers;

    DBMS_OUTPUT.PUT_LINE('Found: ' || v_name);  -- never reached

    DELETE FROM plsql_log WHERE block_id = 'EX-B';
    INSERT INTO plsql_log VALUES ('EX-B', 1, 'Found: ' || v_name);
    COMMIT;

EXCEPTION
    WHEN TOO_MANY_ROWS THEN
        DBMS_OUTPUT.PUT_LINE('TOO_MANY_ROWS: SELECT INTO matched more than one customer row');
        DELETE FROM plsql_log WHERE block_id = 'EX-B';
        INSERT INTO plsql_log VALUES ('EX-B', 1, 'TOO_MANY_ROWS: SELECT INTO matched more than one customer row');
        COMMIT;
END;
/

-- ============================================================
-- Block C: User-defined exception — excessive parcel weight.
-- ============================================================
DECLARE
    excessive_weight EXCEPTION;
    v_weight         NUMBER := 75;   -- exceeds the 50 kg business limit
BEGIN
    IF v_weight > 50 THEN
        RAISE excessive_weight;
    END IF;

    DBMS_OUTPUT.PUT_LINE('Weight ' || v_weight || ' kg is within limit.');  -- not reached

    DELETE FROM plsql_log WHERE block_id = 'EX-C';
    INSERT INTO plsql_log VALUES ('EX-C', 1, 'Weight ' || v_weight || ' kg is within limit.');
    COMMIT;

EXCEPTION
    WHEN excessive_weight THEN
        DBMS_OUTPUT.PUT_LINE('excessive_weight raised: ' || v_weight || ' kg exceeds the 50 kg limit');
        DELETE FROM plsql_log WHERE block_id = 'EX-C';
        INSERT INTO plsql_log VALUES ('EX-C', 1,
            'excessive_weight raised: ' || v_weight || ' kg exceeds the 50 kg limit');
        COMMIT;
END;
/
