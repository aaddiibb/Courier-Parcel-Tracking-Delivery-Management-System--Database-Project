-- 10-alter-constraints.sql
-- Lab 6: adds explicitly-named CHECK and UNIQUE constraints to existing tables.
-- Run as: cdb_admin@XE  Re-runnable: ORA-02264 (name already used) is silently caught.

-- ── parcels: status check ──────────────────────────────────────────────────
BEGIN
    EXECUTE IMMEDIATE
        'ALTER TABLE parcels ADD CONSTRAINT chk_parcel_status
         CHECK (current_status IN (''BOOKED'',''IN_TRANSIT'',''OUT_FOR_DELIVERY'',''DELIVERED'',''RETURNED''))';
EXCEPTION WHEN OTHERS THEN IF SQLCODE = -2264 THEN NULL; ELSE RAISE; END IF;
END;
/

-- ── parcels: weight check ──────────────────────────────────────────────────
BEGIN
    EXECUTE IMMEDIATE
        'ALTER TABLE parcels ADD CONSTRAINT chk_parcel_weight
         CHECK (weight_kg > 0 AND weight_kg <= 50)';
EXCEPTION WHEN OTHERS THEN IF SQLCODE = -2264 THEN NULL; ELSE RAISE; END IF;
END;
/

-- ── parcels: origin <> destination ────────────────────────────────────────
BEGIN
    EXECUTE IMMEDIATE
        'ALTER TABLE parcels ADD CONSTRAINT chk_parcel_branches
         CHECK (origin_branch_id <> destination_branch_id)';
EXCEPTION WHEN OTHERS THEN IF SQLCODE = -2264 THEN NULL; ELSE RAISE; END IF;
END;
/

-- ── delivery_attempts: success_flag check ─────────────────────────────────
BEGIN
    EXECUTE IMMEDIATE
        'ALTER TABLE delivery_attempts ADD CONSTRAINT chk_attempt_flag
         CHECK (success_flag IN (''Y'',''N''))';
EXCEPTION WHEN OTHERS THEN IF SQLCODE = -2264 THEN NULL; ELSE RAISE; END IF;
END;
/

-- ── fees: paid_flag check ─────────────────────────────────────────────────
BEGIN
    EXECUTE IMMEDIATE
        'ALTER TABLE fees ADD CONSTRAINT chk_fee_paid
         CHECK (paid_flag IN (''Y'',''N''))';
EXCEPTION WHEN OTHERS THEN IF SQLCODE = -2264 THEN NULL; ELSE RAISE; END IF;
END;
/

-- ── fees: non-negative total ──────────────────────────────────────────────
BEGIN
    EXECUTE IMMEDIATE
        'ALTER TABLE fees ADD CONSTRAINT chk_fee_total
         CHECK (total_amount >= 0)';
EXCEPTION WHEN OTHERS THEN IF SQLCODE = -2264 THEN NULL; ELSE RAISE; END IF;
END;
/

-- ── riders: active_flag check ─────────────────────────────────────────────
BEGIN
    EXECUTE IMMEDIATE
        'ALTER TABLE riders ADD CONSTRAINT chk_rider_active
         CHECK (active_flag IN (''Y'',''N''))';
EXCEPTION WHEN OTHERS THEN IF SQLCODE = -2264 THEN NULL; ELSE RAISE; END IF;
END;
/

-- ── customers: unique phone ───────────────────────────────────────────────
BEGIN
    EXECUTE IMMEDIATE
        'ALTER TABLE customers ADD CONSTRAINT uq_customer_phone
         UNIQUE (phone)';
EXCEPTION WHEN OTHERS THEN IF SQLCODE = -2264 THEN NULL; ELSE RAISE; END IF;
END;
/

-- ── customers: unique email ───────────────────────────────────────────────
BEGIN
    EXECUTE IMMEDIATE
        'ALTER TABLE customers ADD CONSTRAINT uq_customer_email
         UNIQUE (email)';
EXCEPTION WHEN OTHERS THEN IF SQLCODE = -2264 THEN NULL; ELSE RAISE; END IF;
END;
/

PROMPT Constraints added (chk_parcel_status, chk_parcel_weight, chk_parcel_branches,
PROMPT   chk_attempt_flag, chk_fee_paid, chk_fee_total, chk_rider_active,
PROMPT   uq_customer_phone, uq_customer_email).
