-- 02-create-roles.sql
-- Creates application roles for the courier-db schema.
-- Run as: SYSTEM@XE
-- Re-runnable: ORA-01921 (role already exists) is silently caught.

BEGIN
    EXECUTE IMMEDIATE 'CREATE ROLE role_admin';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE = -1921 THEN NULL; ELSE RAISE; END IF;
END;
/

BEGIN
    EXECUTE IMMEDIATE 'CREATE ROLE role_branch_mgr';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE = -1921 THEN NULL; ELSE RAISE; END IF;
END;
/

BEGIN
    EXECUTE IMMEDIATE 'CREATE ROLE role_rider';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE = -1921 THEN NULL; ELSE RAISE; END IF;
END;
/

BEGIN
    EXECUTE IMMEDIATE 'CREATE ROLE role_customer';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE = -1921 THEN NULL; ELSE RAISE; END IF;
END;
/

PROMPT Roles created (or already existed): role_admin, role_branch_mgr, role_rider, role_customer
