-- 03-receivers.sql
-- Parcel recipients linked to the booking customer.
-- Run as: cdb_admin@XE  Re-runnable.

BEGIN EXECUTE IMMEDIATE 'DROP TABLE receivers';
EXCEPTION WHEN OTHERS THEN IF SQLCODE = -942 THEN NULL; ELSE RAISE; END IF; END;
/

CREATE TABLE receivers (
    receiver_id          NUMBER          CONSTRAINT receivers_pk PRIMARY KEY,
    full_name            VARCHAR2(100)   NOT NULL,
    phone                VARCHAR2(20)    NOT NULL,
    address              VARCHAR2(300),
    booking_customer_id  NUMBER          CONSTRAINT receivers_customer_fk
                                             REFERENCES customers(customer_id)
);

PROMPT Table RECEIVERS created.
