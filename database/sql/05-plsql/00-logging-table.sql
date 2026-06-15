-- 00-logging-table.sql
-- Creates the global temporary table used to capture PL/SQL output for the Laravel web UI.
-- Run as: cdb_admin@XE  Re-runnable.
SET SERVEROUTPUT ON;

DECLARE
    v_count NUMBER;
BEGIN
    SELECT COUNT(*) INTO v_count
    FROM user_tables
    WHERE table_name = 'PLSQL_LOG';

    IF v_count = 0 THEN
        EXECUTE IMMEDIATE '
            CREATE GLOBAL TEMPORARY TABLE plsql_log (
                block_id  VARCHAR2(20),
                line_no   NUMBER,
                message   VARCHAR2(500)
            ) ON COMMIT PRESERVE ROWS
        ';
        DBMS_OUTPUT.PUT_LINE('plsql_log created.');
    ELSE
        DBMS_OUTPUT.PUT_LINE('plsql_log already exists — skipped.');
    END IF;
END;
/
