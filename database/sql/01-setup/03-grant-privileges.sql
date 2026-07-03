
GRANT CREATE TABLE     TO role_admin;
GRANT CREATE SEQUENCE  TO role_admin;
GRANT CREATE PROCEDURE TO role_admin;
GRANT CREATE TRIGGER   TO role_admin;
GRANT CREATE VIEW      TO role_admin;
GRANT CREATE TYPE      TO role_admin;

-- Assign roles to matching users
GRANT role_admin      TO cdb_admin;
GRANT role_branch_mgr TO cdb_branch_mgr;
GRANT role_rider      TO cdb_rider;
GRANT role_customer   TO cdb_customer;

PROMPT System privileges granted and roles assigned.
