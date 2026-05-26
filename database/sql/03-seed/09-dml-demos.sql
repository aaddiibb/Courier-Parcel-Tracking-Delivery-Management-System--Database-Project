-- 09-dml-demos.sql
-- Lab 4 DML demonstrations: 2 UPDATEs + 1 DELETE.
-- Run as: cdb_admin@XE

-- ── Lab 4 · UPDATE #1 ─────────────────────────────────────────────────────
-- Promote parcel 1012 (IN_TRANSIT) to OUT_FOR_DELIVERY.
-- Demonstrates: UPDATE with WHERE on primary key.
UPDATE parcels
SET    current_status = 'OUT_FOR_DELIVERY'
WHERE  parcel_id = 1012;

-- ── Lab 4 · UPDATE #2 ─────────────────────────────────────────────────────
-- Mark the fee for parcel 1003 (BOOKED) as paid.
-- Demonstrates: UPDATE across a related table using a scalar subquery.
UPDATE fees
SET    paid_flag = 'Y',
       paid_at   = SYSDATE
WHERE  parcel_id = 1003;

-- ── Lab 4 · DELETE ────────────────────────────────────────────────────────
-- Remove the failed delivery attempt for parcel 1009 (wrong address).
-- Demonstrates: DELETE with a targeted WHERE clause on a non-PK column.
DELETE FROM delivery_attempts
WHERE  parcel_id    = 1009
AND    success_flag = 'N';

COMMIT;

PROMPT DML demos complete: 2 UPDATEs + 1 DELETE committed.
