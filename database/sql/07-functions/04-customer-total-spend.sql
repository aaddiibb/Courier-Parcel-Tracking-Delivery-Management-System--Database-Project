
CREATE OR REPLACE FUNCTION customer_total_spend(p_customer_id IN NUMBER) RETURN NUMBER IS
    v_total NUMBER;
BEGIN
    SELECT NVL(SUM(f.total_amount), 0)
    INTO v_total
    FROM fees f
    JOIN parcels p ON f.parcel_id = p.parcel_id
    WHERE p.sender_customer_id = p_customer_id
      AND f.paid_flag = 'Y';

    RETURN v_total;
END customer_total_spend;
/

SHOW ERRORS FUNCTION customer_total_spend;

