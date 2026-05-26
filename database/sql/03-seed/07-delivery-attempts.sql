-- 07-delivery-attempts.sql
-- 15 attempts: 10 successful (DELIVERED parcels), 5 failed (various parcels).
-- Parcel IDs 1000–1029; rider IDs 1000–1011.
-- Run as: cdb_admin@XE

-- ── Successful attempts — DELIVERED parcels (1017–1026) ───────────────────
INSERT INTO delivery_attempts (attempt_id,parcel_id,rider_id,attempted_at,success_flag,failure_reason)
VALUES (seq_attempt_id.NEXTVAL,1017,1005,SYSDATE-15,'Y',NULL);

INSERT INTO delivery_attempts (attempt_id,parcel_id,rider_id,attempted_at,success_flag,failure_reason)
VALUES (seq_attempt_id.NEXTVAL,1018,1002,SYSDATE-14,'Y',NULL);

INSERT INTO delivery_attempts (attempt_id,parcel_id,rider_id,attempted_at,success_flag,failure_reason)
VALUES (seq_attempt_id.NEXTVAL,1019,1004,SYSDATE-11,'Y',NULL);

INSERT INTO delivery_attempts (attempt_id,parcel_id,rider_id,attempted_at,success_flag,failure_reason)
VALUES (seq_attempt_id.NEXTVAL,1020,1006,SYSDATE-9, 'Y',NULL);

INSERT INTO delivery_attempts (attempt_id,parcel_id,rider_id,attempted_at,success_flag,failure_reason)
VALUES (seq_attempt_id.NEXTVAL,1021,1009,SYSDATE-10,'Y',NULL);

INSERT INTO delivery_attempts (attempt_id,parcel_id,rider_id,attempted_at,success_flag,failure_reason)
VALUES (seq_attempt_id.NEXTVAL,1022,1011,SYSDATE-7, 'Y',NULL);

INSERT INTO delivery_attempts (attempt_id,parcel_id,rider_id,attempted_at,success_flag,failure_reason)
VALUES (seq_attempt_id.NEXTVAL,1023,1001,SYSDATE-6, 'Y',NULL);

INSERT INTO delivery_attempts (attempt_id,parcel_id,rider_id,attempted_at,success_flag,failure_reason)
VALUES (seq_attempt_id.NEXTVAL,1024,1003,SYSDATE-8, 'Y',NULL);

INSERT INTO delivery_attempts (attempt_id,parcel_id,rider_id,attempted_at,success_flag,failure_reason)
VALUES (seq_attempt_id.NEXTVAL,1025,1005,SYSDATE-5, 'Y',NULL);

INSERT INTO delivery_attempts (attempt_id,parcel_id,rider_id,attempted_at,success_flag,failure_reason)
VALUES (seq_attempt_id.NEXTVAL,1026,1007,SYSDATE-4, 'Y',NULL);

-- ── Failed attempts — IN_TRANSIT and RETURNED parcels ─────────────────────
INSERT INTO delivery_attempts (attempt_id,parcel_id,rider_id,attempted_at,success_flag,failure_reason)
VALUES (seq_attempt_id.NEXTVAL,1005,1000,SYSDATE-5, 'N','Receiver not available at address');

INSERT INTO delivery_attempts (attempt_id,parcel_id,rider_id,attempted_at,success_flag,failure_reason)
VALUES (seq_attempt_id.NEXTVAL,1009,1008,SYSDATE-6, 'N','Incorrect address provided');

INSERT INTO delivery_attempts (attempt_id,parcel_id,rider_id,attempted_at,success_flag,failure_reason)
VALUES (seq_attempt_id.NEXTVAL,1027,1000,SYSDATE-9, 'N','Receiver unavailable — multiple attempts');

INSERT INTO delivery_attempts (attempt_id,parcel_id,rider_id,attempted_at,success_flag,failure_reason)
VALUES (seq_attempt_id.NEXTVAL,1028,1008,SYSDATE-7, 'N','Wrong address — returned to sender');

INSERT INTO delivery_attempts (attempt_id,parcel_id,rider_id,attempted_at,success_flag,failure_reason)
VALUES (seq_attempt_id.NEXTVAL,1029,1010,SYSDATE-5, 'N','Receiver refused the parcel');

PROMPT 15 delivery attempts inserted (10 successful, 5 failed).
