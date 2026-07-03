-- 04-branches.sql
-- Courier branch offices (origin / destination hubs).
-- Run as: cdb_admin@XE  Re-runnable.

BEGIN EXECUTE IMMEDIATE 'DROP TABLE branches';
EXCEPTION WHEN OTHERS THEN IF SQLCODE = -942 THEN NULL; ELSE RAISE; END IF; END;
/

CREATE TABLE branches (
    branch_id    NUMBER          CONSTRAINT branches_pk PRIMARY KEY,
    branch_name  VARCHAR2(100)   NOT NULL CONSTRAINT branches_name_uq UNIQUE,
    city         VARCHAR2(50)    NOT NULL,
    address      VARCHAR2(200),
    phone        VARCHAR2(20),
    manager_name VARCHAR2(100)
);


