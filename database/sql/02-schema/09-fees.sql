-- 09-fees.sql
-- One fee record per parcel (enforced by UNIQUE on parcel_id).
-- Run as: cdb_admin@XE  Re-runnable.

BEGIN EXECUTE IMMEDIATE 'DROP TABLE fees';
EXCEPTION WHEN OTHERS THEN IF SQLCODE = -942 THEN NULL; ELSE RAISE; END IF; END;
/

CREATE TABLE fees (
    fee_id         NUMBER          CONSTRAINT fees_pk PRIMARY KEY,
    parcel_id      NUMBER          CONSTRAINT fees_parcel_fk
                                       REFERENCES parcels(parcel_id)
                                       CONSTRAINT fees_parcel_uq UNIQUE,
    base_amount    NUMBER(8,2),
    weight_charge  NUMBER(8,2),
    total_amount   NUMBER(8,2),
    paid_flag      CHAR(1)         DEFAULT 'N',
    paid_at        DATE
);

PROMPT Table FEES created.
