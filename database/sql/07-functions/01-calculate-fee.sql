-- 01-calculate-fee.sql
-- Tiered parcel fee calculation.
-- Called by book_parcel (database/sql/06-procedures/01-book-parcel.sql) and,
-- once it exists, by trg_auto_fee (Prompt B) — must compile before either.
-- Run as: cdb_admin@XE  Re-runnable (CREATE OR REPLACE).

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
PROMPT Function CALCULATE_FEE compiled.
