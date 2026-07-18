

CREATE OR REPLACE FUNCTION rider_success_rate(p_rider_id IN NUMBER) RETURN NUMBER IS
    v_rate NUMBER;
BEGIN
    SELECT SUM(CASE WHEN success_flag = 'Y' THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(*), 0)
    INTO v_rate
    FROM delivery_attempts
    WHERE rider_id = p_rider_id;

  
    RETURN NVL(ROUND(v_rate, 1), 0);
END rider_success_rate;
/

SHOW ERRORS FUNCTION rider_success_rate;

