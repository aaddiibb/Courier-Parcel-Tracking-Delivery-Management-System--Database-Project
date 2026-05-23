-- 07-parcel-status-history.sql
-- Audit trail of every status change for a parcel.
-- NOTE: Day 11 adds a trigger that auto-inserts rows here whenever
--       parcels.current_status changes. Do NOT create that trigger today.
-- Run as: cdb_admin@XE  Re-runnable.

BEGIN EXECUTE IMMEDIATE 'DROP TABLE parcel_status_history';
EXCEPTION WHEN OTHERS THEN IF SQLCODE = -942 THEN NULL; ELSE RAISE; END IF; END;
/

CREATE TABLE parcel_status_history (
    history_id   NUMBER          CONSTRAINT psh_pk PRIMARY KEY,
    parcel_id    NUMBER          CONSTRAINT psh_parcel_fk
                                     REFERENCES parcels(parcel_id) ON DELETE CASCADE,
    status       VARCHAR2(20),
    changed_at   DATE            DEFAULT SYSDATE,
    changed_by   VARCHAR2(50),
    remarks      VARCHAR2(200)
);

PROMPT Table PARCEL_STATUS_HISTORY created.
