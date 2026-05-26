-- 03-grant-privileges.sql
-- Grants system privileges to roles and assigns roles to users.
-- Run as: SYSTEM@XE
-- Table-level grants for branch_mgr/rider/customer finalized Day 4.
-- after business tables exist in cdb_admin schema.

-- System privileges for schema owner role
GRANT CREATE TABLE     TO role_admin;
GRANT CREATE SEQUENCE  TO role_admin;
GRANT CREATE PROCEDURE TO role_admin;
GRANT CREATE TRIGGER   TO role_admin;
GRANT CREATE VIEW      TO role_admin;
GRANT CREATE TYPE      TO role_admin;

-- Table-level grants for role_branch_mgr (Day 4)
GRANT SELECT, INSERT, UPDATE ON cdb_admin.parcels               TO role_branch_mgr;
GRANT SELECT, INSERT, UPDATE ON cdb_admin.parcel_status_history TO role_branch_mgr;
GRANT SELECT, INSERT, UPDATE ON cdb_admin.delivery_attempts     TO role_branch_mgr;
GRANT SELECT, INSERT, UPDATE ON cdb_admin.fees                  TO role_branch_mgr;

-- Table-level grants for role_rider (Day 4)
GRANT SELECT        ON cdb_admin.parcels TO role_rider;
GRANT SELECT        ON cdb_admin.riders  TO role_rider;
GRANT UPDATE        ON cdb_admin.parcels TO role_rider;

-- Table-level grants for role_customer (Day 4)
GRANT SELECT ON cdb_admin.parcels               TO role_customer;
GRANT SELECT ON cdb_admin.parcel_status_history TO role_customer;

-- Assign roles to matching users
GRANT role_admin      TO cdb_admin;
GRANT role_branch_mgr TO cdb_branch_mgr;
GRANT role_rider      TO cdb_rider;
GRANT role_customer   TO cdb_customer;

PROMPT Privileges granted and roles assigned.
