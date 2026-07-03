-- 12-alter-riders-user-link.sql
-- Links a riders row to the Laravel-managed users table, for rider role
-- scoping in the app (mirrors what 13-alter-customers-user-link.sql does
-- for customers). Run as: cdb_admin@XE  Re-runnable.
-- No formal FK constraint to avoid cross-system complexity — logical link only.

BEGIN
    EXECUTE IMMEDIATE 'ALTER TABLE riders ADD user_id NUMBER';
EXCEPTION WHEN OTHERS THEN IF SQLCODE = -1430 THEN NULL; ELSE RAISE; END IF;
END;
/

PROMPT riders.user_id added (logical link only, no FK).
