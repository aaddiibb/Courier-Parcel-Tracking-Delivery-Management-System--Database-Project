-- 04-customer-total-spend.sql
-- Sum of a customer's paid fees across all their parcels.
-- Called by the customer dashboard's "Total Spend" stat card.
-- Run as: cdb_admin@XE  Re-runnable (CREATE OR REPLACE).

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
PROMPT Function CUSTOMER_TOTAL_SPEND compiled.
