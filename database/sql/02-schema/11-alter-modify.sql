-- 11-alter-modify.sql
-- Lab 3: ALTER TABLE MODIFY examples.
--   1. Widen customers.address from VARCHAR2(300) to VARCHAR2(400).
--   2. Confirm receivers.phone already carries NOT NULL (MODIFY reaffirms it).
-- Run as: cdb_admin@XE

-- Widen customers.address
ALTER TABLE customers MODIFY (address VARCHAR2(400));

-- Reaffirm NOT NULL on receivers.phone (no data change, documents the intent)
ALTER TABLE receivers MODIFY (phone VARCHAR2(20) NOT NULL);

PROMPT customers.address widened to VARCHAR2(400).
PROMPT receivers.phone confirmed NOT NULL.
