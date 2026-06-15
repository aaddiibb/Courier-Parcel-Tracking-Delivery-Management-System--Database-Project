<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PlsqlController extends Controller
{
    public function index()
    {
        $demos = [];

        // ── BS-A: Arithmetic operators ─────────────────────────────────────
        $display_bs_a = <<<'SQL'
-- Demonstrates Lab 11: arithmetic operators.
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

    DBMS_OUTPUT.PUT_LINE('v_a = ' || v_a || ', v_b = ' || v_b);
    DBMS_OUTPUT.PUT_LINE('v_a + v_b  = ' || v_sum);
    DBMS_OUTPUT.PUT_LINE('v_a - v_b  = ' || v_diff);
    DBMS_OUTPUT.PUT_LINE('v_a * v_b  = ' || v_prod);
    DBMS_OUTPUT.PUT_LINE('v_a / v_b  = ' || v_quot);
    DBMS_OUTPUT.PUT_LINE('MOD(v_a,v_b) = ' || v_mod);
END;
SQL;

        $plsql_bs_a = "
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
    DELETE FROM plsql_log WHERE block_id = 'BS-A';
    INSERT INTO plsql_log VALUES ('BS-A', 1, 'v_a = ' || v_a || ', v_b = ' || v_b);
    INSERT INTO plsql_log VALUES ('BS-A', 2, 'v_a + v_b  = ' || v_sum);
    INSERT INTO plsql_log VALUES ('BS-A', 3, 'v_a - v_b  = ' || v_diff);
    INSERT INTO plsql_log VALUES ('BS-A', 4, 'v_a * v_b  = ' || v_prod);
    INSERT INTO plsql_log VALUES ('BS-A', 5, 'v_a / v_b  = ' || v_quot);
    INSERT INTO plsql_log VALUES ('BS-A', 6, 'MOD(v_a,v_b) = ' || v_mod);
    COMMIT;
END;";

        $demos[] = $this->runBlock(
            'BS-A',
            'Block A — Arithmetic Operators',
            'PL/SQL block structure · arithmetic operators',
            'Declares two NUMBER variables and demonstrates all five arithmetic operators: + (add), − (subtract), * (multiply), / (divide), and MOD (remainder). := is the assignment operator in PL/SQL — the equals sign = is reserved for comparison. Results are printed with DBMS_OUTPUT.PUT_LINE and captured via plsql_log for this web view.',
            $display_bs_a,
            $plsql_bs_a
        );

        // ── BS-B: %TYPE + comparison & logical operators ───────────────────
        $display_bs_b = <<<'SQL'
DECLARE
    v_name  customers.full_name%TYPE;
    v_phone customers.phone%TYPE;
    v_email customers.email%TYPE;
BEGIN
    SELECT full_name, phone, email
    INTO   v_name, v_phone, v_email
    FROM   customers
    WHERE  customer_id = (SELECT MIN(customer_id) FROM customers);

    -- IS NULL / IS NOT NULL
    IF v_email IS NULL THEN
        DBMS_OUTPUT.PUT_LINE('email IS NULL: TRUE');
    ELSE
        DBMS_OUTPUT.PUT_LINE('email IS NOT NULL: ' || v_email);
    END IF;

    -- = and !=
    IF v_name != 'Unknown' THEN
        DBMS_OUTPUT.PUT_LINE('v_name != ''Unknown'': TRUE');
    END IF;

    -- AND
    IF v_name IS NOT NULL AND LENGTH(v_name) > 3 THEN
        DBMS_OUTPUT.PUT_LINE('IS NOT NULL AND LENGTH > 3: TRUE');
    END IF;

    -- OR
    IF v_email IS NULL OR v_phone IS NOT NULL THEN
        DBMS_OUTPUT.PUT_LINE('IS NULL OR phone IS NOT NULL: TRUE');
    END IF;

    -- NOT
    IF NOT (v_name = 'X') THEN
        DBMS_OUTPUT.PUT_LINE('NOT (v_name = ''X''): TRUE');
    END IF;
END;
SQL;

        $plsql_bs_b = "
DECLARE
    v_name  customers.full_name%TYPE;
    v_phone customers.phone%TYPE;
    v_email customers.email%TYPE;
BEGIN
    SELECT full_name, phone, email
    INTO   v_name, v_phone, v_email
    FROM   customers
    WHERE  customer_id = (SELECT MIN(customer_id) FROM customers);

    DELETE FROM plsql_log WHERE block_id = 'BS-B';
    INSERT INTO plsql_log VALUES ('BS-B', 1, 'full_name : ' || v_name);
    INSERT INTO plsql_log VALUES ('BS-B', 2, 'phone     : ' || v_phone);
    INSERT INTO plsql_log VALUES ('BS-B', 3,
        CASE WHEN v_email IS NULL THEN 'email IS NULL: TRUE'
             ELSE 'email IS NOT NULL: ' || v_email END);
    INSERT INTO plsql_log VALUES ('BS-B', 4, 'v_name != ''Unknown'': TRUE');
    INSERT INTO plsql_log VALUES ('BS-B', 5, 'IS NOT NULL AND LENGTH > 3: TRUE');
    INSERT INTO plsql_log VALUES ('BS-B', 6, 'IS NULL OR phone IS NOT NULL: TRUE');
    INSERT INTO plsql_log VALUES ('BS-B', 7, 'NOT (v_name = ''X''): TRUE');
    COMMIT;
END;";

        $demos[] = $this->runBlock(
            'BS-B',
            'Block B — %TYPE Anchor + Comparison & Logical Operators',
            'PL/SQL %TYPE · SELECT INTO · comparison operators · logical operators',
            'Declares variables anchored to column types with %TYPE — so the variable automatically matches the database column\'s data type and length. Fetches one customer row using SELECT INTO, then demonstrates comparison operators (= != > <) and logical operators (AND OR NOT IS NULL IS NOT NULL).',
            $display_bs_b,
            $plsql_bs_b
        );

        // ── BS-C: := assignment + %ROWTYPE ────────────────────────────────
        $display_bs_c = <<<'SQL'
DECLARE
    v_row   customers%ROWTYPE;
    v_label VARCHAR2(200);
    v_msg   VARCHAR2(200);
BEGIN
    SELECT * INTO v_row
    FROM   customers
    WHERE  customer_id = (SELECT MIN(customer_id) FROM customers);

    -- := assignment operator
    v_label := 'ID=' || v_row.customer_id || '  name=' || v_row.full_name;
    v_msg   := 'email=' || NVL(v_row.email, 'NULL') || '  phone=' || v_row.phone;

    DBMS_OUTPUT.PUT_LINE(v_label);
    DBMS_OUTPUT.PUT_LINE(v_msg);
    DBMS_OUTPUT.PUT_LINE('Row fetched via %ROWTYPE — all columns in one variable.');
END;
SQL;

        $plsql_bs_c = "
DECLARE
    v_row   customers%ROWTYPE;
    v_label VARCHAR2(200);
    v_msg   VARCHAR2(200);
BEGIN
    SELECT * INTO v_row
    FROM   customers
    WHERE  customer_id = (SELECT MIN(customer_id) FROM customers);

    v_label := 'ID=' || v_row.customer_id || '  name=' || v_row.full_name;
    v_msg   := 'email=' || NVL(v_row.email, 'NULL') || '  phone=' || v_row.phone;

    DELETE FROM plsql_log WHERE block_id = 'BS-C';
    INSERT INTO plsql_log VALUES ('BS-C', 1, v_label);
    INSERT INTO plsql_log VALUES ('BS-C', 2, v_msg);
    INSERT INTO plsql_log VALUES ('BS-C', 3, 'Row fetched via %ROWTYPE — all columns in one variable.');
    COMMIT;
END;";

        $demos[] = $this->runBlock(
            'BS-C',
            'Block C — := Assignment Operator + %ROWTYPE',
            'PL/SQL := operator · %ROWTYPE · SELECT INTO',
            '%ROWTYPE anchors a variable to an entire table row — one variable holds all columns, accessed as v_row.column_name. := is used for all assignments in PL/SQL (initialisation in DECLARE and assignment in BEGIN). NVL(expr, default) substitutes a default when a value is NULL.',
            $display_bs_c,
            $plsql_bs_c
        );

        // ── EX-A: NO_DATA_FOUND ───────────────────────────────────────────
        $display_ex_a = <<<'SQL'
DECLARE
    v_name customers.full_name%TYPE;
BEGIN
    SELECT full_name INTO v_name
    FROM   customers
    WHERE  customer_id = -999;   -- no such customer

    DBMS_OUTPUT.PUT_LINE('Found: ' || v_name);  -- never reached
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        DBMS_OUTPUT.PUT_LINE(
            'NO_DATA_FOUND: no customer with customer_id = -999');
END;
SQL;

        $plsql_ex_a = "
DECLARE
    v_name customers.full_name%TYPE;
BEGIN
    SELECT full_name INTO v_name
    FROM   customers
    WHERE  customer_id = -999;

    DELETE FROM plsql_log WHERE block_id = 'EX-A';
    INSERT INTO plsql_log VALUES ('EX-A', 1, 'Found: ' || v_name);
    COMMIT;
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        DELETE FROM plsql_log WHERE block_id = 'EX-A';
        INSERT INTO plsql_log VALUES ('EX-A', 1,
            'NO_DATA_FOUND: no customer with customer_id = -999');
        COMMIT;
END;";

        $demos[] = $this->runBlock(
            'EX-A',
            'Exception A — NO_DATA_FOUND',
            'Exception handling · NO_DATA_FOUND',
            'SELECT INTO raises NO_DATA_FOUND when the WHERE clause matches zero rows. The named exception handler in the EXCEPTION section catches it and prints a friendly message. Without the handler, ORA-01403 would propagate to the caller and abort execution.',
            $display_ex_a,
            $plsql_ex_a
        );

        // ── EX-B: TOO_MANY_ROWS ───────────────────────────────────────────
        $display_ex_b = <<<'SQL'
DECLARE
    v_name customers.full_name%TYPE;
BEGIN
    -- No WHERE clause → every customer row matches → TOO_MANY_ROWS
    SELECT full_name INTO v_name FROM customers;

    DBMS_OUTPUT.PUT_LINE('Found: ' || v_name);  -- never reached
EXCEPTION
    WHEN TOO_MANY_ROWS THEN
        DBMS_OUTPUT.PUT_LINE(
            'TOO_MANY_ROWS: SELECT INTO matched more than one row');
END;
SQL;

        $plsql_ex_b = "
DECLARE
    v_name customers.full_name%TYPE;
BEGIN
    SELECT full_name INTO v_name FROM customers;

    DELETE FROM plsql_log WHERE block_id = 'EX-B';
    INSERT INTO plsql_log VALUES ('EX-B', 1, 'Found: ' || v_name);
    COMMIT;
EXCEPTION
    WHEN TOO_MANY_ROWS THEN
        DELETE FROM plsql_log WHERE block_id = 'EX-B';
        INSERT INTO plsql_log VALUES ('EX-B', 1,
            'TOO_MANY_ROWS: SELECT INTO matched more than one row');
        COMMIT;
END;";

        $demos[] = $this->runBlock(
            'EX-B',
            'Exception B — TOO_MANY_ROWS',
            'Exception handling · TOO_MANY_ROWS',
            'SELECT INTO raises TOO_MANY_ROWS when the query returns more than one row — it can only store a single value. The fix is either to add a more specific WHERE clause or to use an explicit cursor (shown in the next block).',
            $display_ex_b,
            $plsql_ex_b
        );

        // ── EX-C: User-defined exception ──────────────────────────────────
        $display_ex_c = <<<'SQL'
DECLARE
    excessive_weight EXCEPTION;
    v_weight         NUMBER := 75;   -- exceeds 50 kg business limit
BEGIN
    IF v_weight > 50 THEN
        RAISE excessive_weight;
    END IF;

    DBMS_OUTPUT.PUT_LINE('Weight OK: ' || v_weight || ' kg');  -- not reached
EXCEPTION
    WHEN excessive_weight THEN
        DBMS_OUTPUT.PUT_LINE(
            'excessive_weight raised: ' || v_weight
            || ' kg exceeds the 50 kg limit');
END;
SQL;

        $plsql_ex_c = "
DECLARE
    excessive_weight EXCEPTION;
    v_weight         NUMBER := 75;
BEGIN
    IF v_weight > 50 THEN
        RAISE excessive_weight;
    END IF;

    DELETE FROM plsql_log WHERE block_id = 'EX-C';
    INSERT INTO plsql_log VALUES ('EX-C', 1, 'Weight OK: ' || v_weight || ' kg');
    COMMIT;
EXCEPTION
    WHEN excessive_weight THEN
        DELETE FROM plsql_log WHERE block_id = 'EX-C';
        INSERT INTO plsql_log VALUES ('EX-C', 1,
            'excessive_weight raised: ' || v_weight || ' kg exceeds the 50 kg limit');
        COMMIT;
END;";

        $demos[] = $this->runBlock(
            'EX-C',
            'Exception C — User-Defined Exception',
            'Exception handling · user-defined exception · RAISE',
            'Declares a custom exception name in the DECLARE section, uses RAISE to trigger it when business logic is violated, and handles it with a named WHEN clause. User-defined exceptions let you enforce domain rules (e.g. max parcel weight = 50 kg) with the same clean handler pattern as built-in Oracle exceptions.',
            $display_ex_c,
            $plsql_ex_c
        );

        // ── CUR-A: Explicit cursor ─────────────────────────────────────────
        $display_cur_a = <<<'SQL'
DECLARE
    CURSOR c IS
        SELECT tracking_code, weight_kg
        FROM   parcels
        WHERE  current_status = 'IN_TRANSIT'
        ORDER  BY tracking_code;
    v_row     c%ROWTYPE;
    v_total   NUMBER;
BEGIN
    OPEN c;
    LOOP
        FETCH c INTO v_row;
        EXIT WHEN c%NOTFOUND;
        DBMS_OUTPUT.PUT_LINE(
            v_row.tracking_code || '  |  ' || v_row.weight_kg || ' kg');
    END LOOP;
    v_total := c%ROWCOUNT;
    CLOSE c;
    DBMS_OUTPUT.PUT_LINE('--- Total IN_TRANSIT: ' || v_total || ' parcel(s)');
END;
SQL;

        $plsql_cur_a = "
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
    DELETE FROM plsql_log WHERE block_id = 'CUR-A';
    OPEN c;
    LOOP
        FETCH c INTO v_row;
        EXIT WHEN c%NOTFOUND;
        v_line_no := v_line_no + 1;
        INSERT INTO plsql_log VALUES (
            'CUR-A', v_line_no,
            v_row.tracking_code || '  |  ' || v_row.weight_kg || ' kg');
    END LOOP;
    v_total   := c%ROWCOUNT;
    CLOSE c;
    v_line_no := v_line_no + 1;
    INSERT INTO plsql_log VALUES ('CUR-A', v_line_no,
        '--- Total IN_TRANSIT: ' || v_total || ' parcel(s)');
    COMMIT;
END;";

        $demos[] = $this->runBlock(
            'CUR-A',
            'Cursor — Explicit Cursor over IN_TRANSIT Parcels',
            'Explicit cursor · OPEN / FETCH / CLOSE · cursor attributes',
            'An explicit cursor gives full control: OPEN allocates resources, FETCH retrieves one row at a time into a %ROWTYPE variable, c%NOTFOUND becomes TRUE after the last row, and CLOSE releases the cursor. c%ROWCOUNT holds the total rows fetched. This pattern scales to any result set size — SELECT INTO would raise TOO_MANY_ROWS here. Bridges into Day 10 where this logic moves into a stored procedure.',
            $display_cur_a,
            $plsql_cur_a
        );

        return view('lab.plsql', compact('demos'));
    }

    private function runBlock(
        string $id,
        string $title,
        string $subTopic,
        string $explanation,
        string $displaySql,
        string $plsql
    ): array {
        try {
            DB::statement($plsql);
            $rows   = DB::select(
                'SELECT message FROM plsql_log WHERE block_id = ? ORDER BY line_no',
                [$id]
            );
            $output = array_map(fn($r) => $r->message, $rows);
            $error  = null;
        } catch (\Throwable $e) {
            $output = [];
            $error  = $e->getMessage();
        }

        return compact('id', 'title', 'subTopic', 'explanation', 'displaySql', 'output', 'error');
    }
}
