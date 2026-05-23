-- 08-delivery-attempts.sql
-- Records each delivery attempt (successful or failed) by a rider.
-- Run as: cdb_admin@XE  Re-runnable.

BEGIN EXECUTE IMMEDIATE 'DROP TABLE delivery_attempts';
EXCEPTION WHEN OTHERS THEN IF SQLCODE = -942 THEN NULL; ELSE RAISE; END IF; END;
/

CREATE TABLE delivery_attempts (
    attempt_id      NUMBER          CONSTRAINT da_pk PRIMARY KEY,
    parcel_id       NUMBER          CONSTRAINT da_parcel_fk
                                        REFERENCES parcels(parcel_id),
    rider_id        NUMBER          CONSTRAINT da_rider_fk
                                        REFERENCES riders(rider_id),
    attempted_at    DATE            DEFAULT SYSDATE,
    success_flag    CHAR(1),
    failure_reason  VARCHAR2(100)
);

PROMPT Table DELIVERY_ATTEMPTS created.
