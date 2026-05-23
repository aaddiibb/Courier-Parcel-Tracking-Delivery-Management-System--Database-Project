-- 06-parcels.sql
-- Core parcel entity — central to all business operations.
-- Run as: cdb_admin@XE  Re-runnable.

BEGIN EXECUTE IMMEDIATE 'DROP TABLE parcels';
EXCEPTION WHEN OTHERS THEN IF SQLCODE = -942 THEN NULL; ELSE RAISE; END IF; END;
/

CREATE TABLE parcels (
    parcel_id              NUMBER          CONSTRAINT parcels_pk PRIMARY KEY,
    tracking_code          VARCHAR2(20)    NOT NULL CONSTRAINT parcels_code_uq UNIQUE,
    sender_customer_id     NUMBER          CONSTRAINT parcels_sender_fk
                                               REFERENCES customers(customer_id),
    receiver_id            NUMBER          CONSTRAINT parcels_receiver_fk
                                               REFERENCES receivers(receiver_id),
    origin_branch_id       NUMBER          CONSTRAINT parcels_origin_fk
                                               REFERENCES branches(branch_id),
    destination_branch_id  NUMBER          CONSTRAINT parcels_dest_fk
                                               REFERENCES branches(branch_id),
    assigned_rider_id      NUMBER          CONSTRAINT parcels_rider_fk
                                               REFERENCES riders(rider_id),
    weight_kg              NUMBER(6,2),
    current_status         VARCHAR2(20)    DEFAULT 'BOOKED',
    booked_at              DATE            DEFAULT SYSDATE,
    delivered_at           DATE
);

PROMPT Table PARCELS created.
