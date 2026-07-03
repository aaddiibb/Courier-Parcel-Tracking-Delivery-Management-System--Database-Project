-- 03-rider-success-rate.sql
-- Percentage of a rider's delivery attempts that succeeded.
-- Called by the Rider Performance report (Prompt C).
-- Run as: cdb_admin@XE  Re-runnable (CREATE OR REPLACE).

CREATE OR REPLACE FUNCTION rider_success_rate(p_rider_id IN NUMBER) RETURN NUMBER IS
    v_rate NUMBER;
BEGIN
    SELECT SUM(CASE WHEN success_flag = 'Y' THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(*), 0)
    INTO v_rate
    FROM delivery_attempts
    WHERE rider_id = p_rider_id;

    -- NULLIF(COUNT(*),0) is NULL when the rider has zero attempts, which makes
    -- v_rate NULL rather than raising an error — NVL below turns that into 0.
    RETURN NVL(ROUND(v_rate, 1), 0);
END rider_success_rate;
/

SHOW ERRORS FUNCTION rider_success_rate;
PROMPT Function RIDER_SUCCESS_RATE compiled.
