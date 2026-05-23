-- 01-create-users.sql
-- Creates application users for the courier-db schema.
-- DEMO ONLY: passwords are intentionally simple for academic use.
-- Run as: SYSTEM@XE
-- Re-runnable: ORA-01920 (user already exists) is silently caught.

-- cdb_admin: schema owner, all business tables live here
BEGIN
    EXECUTE IMMEDIATE 'CREATE USER cdb_admin IDENTIFIED BY Admin1234';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE = -1920 THEN NULL; ELSE RAISE; END IF;
END;
/
GRANT CREATE SESSION TO cdb_admin;
ALTER USER cdb_admin DEFAULT TABLESPACE USERS;
ALTER USER cdb_admin QUOTA 100M ON USERS;

-- cdb_branch_mgr: branch manager role user
BEGIN
    EXECUTE IMMEDIATE 'CREATE USER cdb_branch_mgr IDENTIFIED BY Mgr1234';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE = -1920 THEN NULL; ELSE RAISE; END IF;
END;
/
GRANT CREATE SESSION TO cdb_branch_mgr;

-- cdb_rider: delivery rider role user
BEGIN
    EXECUTE IMMEDIATE 'CREATE USER cdb_rider IDENTIFIED BY Rider1234';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE = -1920 THEN NULL; ELSE RAISE; END IF;
END;
/
GRANT CREATE SESSION TO cdb_rider;

-- cdb_customer: customer role user
BEGIN
    EXECUTE IMMEDIATE 'CREATE USER cdb_customer IDENTIFIED BY Cust1234';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE = -1920 THEN NULL; ELSE RAISE; END IF;
END;
/
GRANT CREATE SESSION TO cdb_customer;

PROMPT Users created (or already existed): cdb_admin, cdb_branch_mgr, cdb_rider, cdb_customer
