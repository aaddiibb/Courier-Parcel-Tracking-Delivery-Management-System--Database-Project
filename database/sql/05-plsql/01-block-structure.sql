-- 01-block-structure.sql
-- Lab 11: PL/SQL block structure, operators.
-- Run as: cdb_admin@XE
SET SERVEROUTPUT ON;

-- ============================================================
-- Block A: Demonstrates Lab 11: arithmetic operators.
-- ============================================================
DECLARE
    v_a    NUMBER := 42;
    v_b    NUMBER := 5;
    v_sum  NUMBER;
    v_diff NUMBER;
    v_prod NUMBER;
    v_quot NUMBER;
    v_mod  NUMBER;
BEGIN
    v_sum  := v_a + v_b;
    v_diff := v_a - v_b;
    v_prod := v_a * v_b;
    v_quot := v_a / v_b;
    v_mod  := MOD(v_a, v_b);

    DBMS_OUTPUT.PUT_LINE('=== Block A: Arithmetic Operators ===');
    DBMS_OUTPUT.PUT_LINE('v_a = ' || v_a || ', v_b = ' || v_b);
    DBMS_OUTPUT.PUT_LINE('v_a + v_b  = ' || v_sum);
    DBMS_OUTPUT.PUT_LINE('v_a - v_b  = ' || v_diff);
    DBMS_OUTPUT.PUT_LINE('v_a * v_b  = ' || v_prod);
    DBMS_OUTPUT.PUT_LINE('v_a / v_b  = ' || v_quot);
    DBMS_OUTPUT.PUT_LINE('MOD(v_a,v_b) = ' || v_mod);

    DELETE FROM plsql_log WHERE block_id = 'BS-A';
    INSERT INTO plsql_log VALUES ('BS-A', 1, 'v_a = ' || v_a || ', v_b = ' || v_b);
    INSERT INTO plsql_log VALUES ('BS-A', 2, 'v_a + v_b  = ' || v_sum);
    INSERT INTO plsql_log VALUES ('BS-A', 3, 'v_a - v_b  = ' || v_diff);
    INSERT INTO plsql_log VALUES ('BS-A', 4, 'v_a * v_b  = ' || v_prod);
    INSERT INTO plsql_log VALUES ('BS-A', 5, 'v_a / v_b  = ' || v_quot);
    INSERT INTO plsql_log VALUES ('BS-A', 6, 'MOD(v_a,v_b) = ' || v_mod);
    COMMIT;
END;
/

-- ============================================================
-- Block B: %TYPE, SELECT INTO, comparison + logical operators.
-- ============================================================
DECLARE
    v_name  customers.full_name%TYPE;
    v_phone customers.phone%TYPE;
    v_email customers.email%TYPE;
BEGIN
    SELECT full_name, phone, email
    INTO   v_name, v_phone, v_email
    FROM   customers
    WHERE  customer_id = (SELECT MIN(customer_id) FROM customers);

    DBMS_OUTPUT.PUT_LINE('=== Block B: %TYPE + Comparison & Logical Operators ===');
    DBMS_OUTPUT.PUT_LINE('full_name : ' || v_name);
    DBMS_OUTPUT.PUT_LINE('phone     : ' || v_phone);

    -- IS NULL / IS NOT NULL
    IF v_email IS NULL THEN
        DBMS_OUTPUT.PUT_LINE('email IS NULL: TRUE');
    ELSE
        DBMS_OUTPUT.PUT_LINE('email IS NOT NULL: TRUE  (' || v_email || ')');
    END IF;

    -- = and !=
    IF v_name != 'Unknown' THEN
        DBMS_OUTPUT.PUT_LINE('v_name != ''Unknown'': TRUE');
    END IF;

    -- AND / OR
    IF v_name IS NOT NULL AND LENGTH(v_name) > 3 THEN
        DBMS_OUTPUT.PUT_LINE('v_name IS NOT NULL AND LENGTH > 3: TRUE');
    END IF;

    IF v_email IS NULL OR v_phone IS NOT NULL THEN
        DBMS_OUTPUT.PUT_LINE('v_email IS NULL OR v_phone IS NOT NULL: TRUE');
    END IF;

    -- NOT
    IF NOT (v_name = 'X') THEN
        DBMS_OUTPUT.PUT_LINE('NOT (v_name = ''X''): TRUE');
    END IF;

    DELETE FROM plsql_log WHERE block_id = 'BS-B';
    INSERT INTO plsql_log VALUES ('BS-B', 1, 'full_name : ' || v_name);
    INSERT INTO plsql_log VALUES ('BS-B', 2, 'phone     : ' || v_phone);
    INSERT INTO plsql_log VALUES ('BS-B', 3,
        CASE WHEN v_email IS NULL THEN 'email IS NULL: TRUE'
             ELSE 'email IS NOT NULL: TRUE  (' || v_email || ')' END);
    INSERT INTO plsql_log VALUES ('BS-B', 4, 'v_name != ''Unknown'': TRUE');
    INSERT INTO plsql_log VALUES ('BS-B', 5, 'v_name IS NOT NULL AND LENGTH > 3: TRUE');
    INSERT INTO plsql_log VALUES ('BS-B', 6, 'v_email IS NULL OR v_phone IS NOT NULL: TRUE');
    INSERT INTO plsql_log VALUES ('BS-B', 7, 'NOT (v_name = ''X''): TRUE');
    COMMIT;
END;
/

-- ============================================================
-- Block C: := assignment operator + %ROWTYPE.
-- ============================================================
DECLARE
    v_row   customers%ROWTYPE;
    v_label VARCHAR2(200);
    v_msg   VARCHAR2(200);
BEGIN
    SELECT * INTO v_row
    FROM   customers
    WHERE  customer_id = (SELECT MIN(customer_id) FROM customers);

    -- := assignment
    v_label := 'ID=' || v_row.customer_id || '  name=' || v_row.full_name;
    v_msg   := 'email=' || NVL(v_row.email, 'NULL') || '  phone=' || v_row.phone;

    DBMS_OUTPUT.PUT_LINE('=== Block C: := Assignment + %ROWTYPE ===');
    DBMS_OUTPUT.PUT_LINE(v_label);
    DBMS_OUTPUT.PUT_LINE(v_msg);
    DBMS_OUTPUT.PUT_LINE('Row fetched via %ROWTYPE — all columns in one variable.');

    DELETE FROM plsql_log WHERE block_id = 'BS-C';
    INSERT INTO plsql_log VALUES ('BS-C', 1, v_label);
    INSERT INTO plsql_log VALUES ('BS-C', 2, v_msg);
    INSERT INTO plsql_log VALUES ('BS-C', 3, 'Row fetched via %ROWTYPE — all columns in one variable.');
    COMMIT;
END;
/
