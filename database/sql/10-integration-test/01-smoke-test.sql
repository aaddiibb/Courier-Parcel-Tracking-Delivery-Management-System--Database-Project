-- 01-smoke-test.sql
-- Real regression test for the courier system's PL/SQL layer: books a
-- parcel, exercises every trigger/procedure in the status-change chain, and
-- rolls back all of it at the end. Not a lab demo — this is what should be
-- run after any change to a procedure, function, or trigger to confirm the
-- chain still holds together.
-- Run as: cdb_admin@XE

SET SERVEROUTPUT ON;
DECLARE
  v_tc     VARCHAR2(30);
  v_pid    NUMBER;
  v_status VARCHAR2(20);
  v_cnt    NUMBER;
  v_pass   NUMBER := 0;
  v_fail   NUMBER := 0;

  PROCEDURE assert(label VARCHAR2, condition BOOLEAN) IS
  BEGIN
    IF condition THEN
      DBMS_OUTPUT.PUT_LINE('  PASS: ' || label);
      v_pass := v_pass + 1;
    ELSE
      DBMS_OUTPUT.PUT_LINE('  FAIL: ' || label);
      v_fail := v_fail + 1;
    END IF;
  END;

BEGIN
  DBMS_OUTPUT.PUT_LINE('=== Courier System Integration Test ===');

  -- Test 1: Book a parcel
  book_parcel(1000, 1000, 1000, 1001, 7.5, 'SMOKE_TEST', v_tc);
  -- Tracking code format is 'CDB' || YYYY (4 digits) || 5-digit padded id
  -- = 3 + 4 + 5 = 12 characters, not 14 — confirmed against real generated
  -- codes (e.g. CDB202601309) before writing this assertion.
  assert('book_parcel returns tracking code', v_tc IS NOT NULL AND LENGTH(v_tc) = 12);

  SELECT parcel_id INTO v_pid FROM parcels WHERE tracking_code = v_tc;

  -- Test 2: Fee auto-created by trigger
  SELECT COUNT(*) INTO v_cnt FROM fees WHERE parcel_id = v_pid;
  assert('trg_auto_fee created fee row', v_cnt = 1);

  -- Test 3: Initial history row exists (BOOKED, inserted by procedure)
  SELECT COUNT(*) INTO v_cnt FROM parcel_status_history WHERE parcel_id = v_pid AND status = 'BOOKED';
  assert('book_parcel inserted initial BOOKED history', v_cnt = 1);

  -- Test 4: Status transition BOOKED -> IN_TRANSIT (trigger logs it)
  -- NOTE: a procedure call cannot take a bare (SELECT ...) subquery as an
  -- argument the way a SQL statement can — that's only valid inside SQL,
  -- not a plain PL/SQL call (confirmed: ORA-06550/PLS-00103 when tried).
  -- v_pid, resolved once above via SELECT INTO, is reused everywhere below.
  update_parcel_status(v_pid, 'IN_TRANSIT', 'SMOKE_TEST', NULL);
  SELECT COUNT(*) INTO v_cnt FROM parcel_status_history WHERE parcel_id = v_pid AND status = 'IN_TRANSIT';
  assert('trg_status_history logged IN_TRANSIT', v_cnt = 1);

  -- Test 5: Invalid transition rejected
  BEGIN
    update_parcel_status(v_pid, 'BOOKED', 'SMOKE_TEST', NULL);
    assert('invalid transition rejected', FALSE); -- should not reach here
  EXCEPTION
    WHEN OTHERS THEN
      assert('invalid transition correctly rejected', SQLCODE = -20006 OR SQLCODE = -20007);
  END;

  -- Test 6: 3 failed attempts -> auto-return
  BEGIN
    update_parcel_status(v_pid, 'OUT_FOR_DELIVERY', 'SMOKE_TEST', NULL);
    INSERT INTO delivery_attempts (attempt_id, parcel_id, rider_id, attempted_at, success_flag, failure_reason) VALUES (seq_attempt_id.NEXTVAL, v_pid, 1000, SYSDATE, 'N', 'Test 1');
    INSERT INTO delivery_attempts (attempt_id, parcel_id, rider_id, attempted_at, success_flag, failure_reason) VALUES (seq_attempt_id.NEXTVAL, v_pid, 1000, SYSDATE, 'N', 'Test 2');
    INSERT INTO delivery_attempts (attempt_id, parcel_id, rider_id, attempted_at, success_flag, failure_reason) VALUES (seq_attempt_id.NEXTVAL, v_pid, 1000, SYSDATE, 'N', 'Test 3');
    SELECT current_status INTO v_status FROM parcels WHERE parcel_id = v_pid;
    assert('trg_auto_return set status to RETURNED', v_status = 'RETURNED');
    SELECT COUNT(*) INTO v_cnt FROM parcel_status_history WHERE parcel_id = v_pid AND status = 'RETURNED';
    assert('trg_status_history logged RETURNED', v_cnt = 1);
  END;

  -- Test 7: calculate_fee function
  assert('calculate_fee(3) = 110', calculate_fee(3) = 110);   -- 50 + 3*20 = 110
  assert('calculate_fee(10) = 330', calculate_fee(10) = 330); -- 80 + 10*25 = 330
  assert('calculate_fee(20) = 750', calculate_fee(20) = 750); -- 150 + 20*30 = 750

  ROLLBACK; -- Clean up all smoke test data

  DBMS_OUTPUT.PUT_LINE('=== Results: ' || v_pass || ' passed, ' || v_fail || ' failed ===');
END;
/
