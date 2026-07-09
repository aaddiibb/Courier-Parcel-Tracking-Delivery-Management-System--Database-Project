-- 02-trg-auto-fee.sql
-- Auto-creates a fee row for every new parcel.
--
-- Replaces the manual fee INSERT that was previously in
-- Admin\ParcelController@store and Customer\ParcelController@store. Those
-- inserts were removed when book_parcel was wired in (Prompt A).
--
-- Fires after EVERY parcel insert — whether from the book_parcel procedure
-- or a direct INSERT (e.g. seed data re-runs) — which is why
-- database/sql/03-seed/08-fees.sql was cleared: re-running the seed insert
-- after this trigger exists would double-insert (or violate the fees.parcel_id
-- UNIQUE constraint on any parcel_id already given a trigger-created row).
--
-- Depends on: calculate_fee (07-functions/01-calculate-fee.sql) — must be
-- compiled first, enforced by load order in run-all.sql.
--
-- Run as: cdb_admin@XE  Re-runnable (CREATE OR REPLACE).

CREATE OR REPLACE TRIGGER trg_auto_fee
AFTER INSERT ON parcels
FOR EACH ROW
DECLARE
    v_fee       NUMBER;
    v_base      NUMBER := 50;
    v_wt_charge NUMBER;
BEGIN
    v_fee       := calculate_fee(:NEW.weight_kg);
    v_wt_charge := :NEW.weight_kg * 20;

    INSERT INTO fees (fee_id, parcel_id, base_amount, weight_charge, total_amount, paid_flag)
    VALUES (seq_fee_id.NEXTVAL, :NEW.parcel_id, v_base, v_wt_charge, v_fee, 'N');
END;
/

SHOW ERRORS TRIGGER trg_auto_fee;
PROMPT Trigger TRG_AUTO_FEE compiled.
