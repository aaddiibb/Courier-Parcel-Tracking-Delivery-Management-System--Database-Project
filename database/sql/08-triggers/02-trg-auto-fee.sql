
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

