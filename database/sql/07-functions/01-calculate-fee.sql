
CREATE OR REPLACE FUNCTION calculate_fee(p_weight IN NUMBER) RETURN NUMBER IS
    v_total NUMBER(8,2);
BEGIN
    IF p_weight <= 5 THEN
        v_total := 50 + (p_weight * 20);
    ELSIF p_weight <= 15 THEN
        v_total := 80 + (p_weight * 25);
    ELSE
        v_total := 150 + (p_weight * 30);
    END IF;

    RETURN v_total;
END calculate_fee;
/

SHOW ERRORS FUNCTION calculate_fee;

