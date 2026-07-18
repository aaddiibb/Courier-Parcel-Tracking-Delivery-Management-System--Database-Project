
CREATE OR REPLACE FUNCTION branch_revenue(p_branch_id IN NUMBER) RETURN NUMBER IS
    v_total NUMBER;
BEGIN
    SELECT NVL(SUM(f.total_amount), 0)
    INTO v_total
    FROM fees f
    JOIN parcels p ON f.parcel_id = p.parcel_id
    WHERE p.origin_branch_id = p_branch_id
      AND f.paid_flag = 'Y';

    RETURN v_total;
END branch_revenue;
/

SHOW ERRORS FUNCTION branch_revenue;

