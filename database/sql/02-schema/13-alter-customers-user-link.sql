-- 13-alter-customers-user-link.sql
-- Links a customers row to its Laravel users row, same pattern needed later
-- for branches/riders (see Day 10 follow-up note in docs/PROGRESS.md).
-- Run as: cdb_admin@XE  Re-runnable: ORA-01430 (column exists) / ORA-02264 (name used) silently caught.

BEGIN
    EXECUTE IMMEDIATE 'ALTER TABLE customers ADD user_id NUMBER';
EXCEPTION WHEN OTHERS THEN IF SQLCODE = -1430 THEN NULL; ELSE RAISE; END IF;
END;
/

BEGIN
    EXECUTE IMMEDIATE
        'ALTER TABLE customers ADD CONSTRAINT fk_customers_user
         FOREIGN KEY (user_id) REFERENCES users(id)';
EXCEPTION WHEN OTHERS THEN IF SQLCODE = -2264 THEN NULL; ELSE RAISE; END IF;
END;
/

BEGIN
    EXECUTE IMMEDIATE
        'ALTER TABLE customers ADD CONSTRAINT uq_customers_user
         UNIQUE (user_id)';
EXCEPTION WHEN OTHERS THEN IF SQLCODE = -2264 THEN NULL; ELSE RAISE; END IF;
END;
/

PROMPT customers.user_id added (FK + UNIQUE to users.id).
