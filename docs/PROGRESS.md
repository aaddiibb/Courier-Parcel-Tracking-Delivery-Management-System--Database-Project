# Progress Log

## Day 1 — Completed

### Phase A — Environment Diagnosis
- PHP 8.2.12 (ZTS x64) via XAMPP confirmed
- Composer 2.9.5 confirmed
- sqlplus found at `C:\oraclexe\app\oracle\product\11.2.0\server\bin`
- Oracle XE connection verified: `SELECT 1 FROM DUAL` returned 1

### Phase B — OCI8 Enabled
- Downloaded `php_oci8_19.dll` from PECL (OCI8 3.x, PHP 8.2 TS x64)
- Root cause of load failure: Oracle 11g XE `oci.dll` lacks `OCIStmtGetNextResult` (12c+ function)
- Fix: Oracle Instant Client 23.0 DLLs copied to `C:\xampp\php\` and `C:\xampp\apache\bin\`
- `php.ini`: `extension=oci8_19` uncommented
- Verified: `php -m` returns `oci8`

### Phase C — Laravel Project Created
- Laravel 12.12.2 installed at `C:\xampp\htdocs\courier-db` (PHP 8.2 constraint resolved automatically)
- `yajra/laravel-oci8` v12.11.0 installed
- Oracle vendor config published to `config/oracle.php`

### Phase D — Database Configured
- `.env` set: `DB_CONNECTION=oracle`, host `localhost:1521`, database/service `XE`, user `system`

### Phase E — Laravel–Oracle Gate PASSED
- `DB::connection()->getPdo()` → `Yajra\Pdo\Oci8` object with live oci8 connection
- `DB::select('SELECT 1 + 1 AS RESULT FROM DUAL')` → `[{result: 2}]`

### Phase F — Project Structure Created
- `docs/`, `database/sql/` subdirectories, `resources/views/lab/` scaffolded

## Next: Day 2 — Oracle users + Laravel auth scaffold

## Day 2 — Completed

### Phase A — Oracle Users, Roles & Privileges
- 4 Oracle users created: `cdb_admin`, `cdb_branch_mgr`, `cdb_rider`, `cdb_customer`
- `cdb_admin` default tablespace set to `USERS` with 100M quota (fix: original script omitted DEFAULT TABLESPACE)
- 4 roles created: `role_admin`, `role_branch_mgr`, `role_rider`, `role_customer`
- System privileges granted to `role_admin`: CREATE TABLE, SEQUENCE, PROCEDURE, TRIGGER, VIEW, TYPE
- Table-level grants for other roles are **commented-out stubs** — will uncomment Day 4 after tables exist
- Roles assigned to matching users
- Scripts in `database/sql/01-setup/`, added to `run-all.sql`
- `.env` switched from `system` to `cdb_admin` — tinker confirms `SELECT USER FROM DUAL` → `CDB_ADMIN`

### Phase B — Laravel Breeze + Migrations
- Breeze v2.4.2 installed with Blade stack; assets compiled via Vite
- Migrations ran against Oracle with **zero identifier-length errors** (all auto-generated names ≤ 30 chars)
- Extra gotcha fixed: ORA-01950 (no privileges on tablespace SYSTEM) — resolved by setting cdb_admin DEFAULT TABLESPACE USERS
- `role` VARCHAR2(20) DEFAULT 'customer' column added to `users` table via separate migration
- Test admin account created: `adib@gmail.com` — role set to `admin` via sqlplus

### Notes
- Table-level grants in `03-grant-privileges.sql` are stubbed — uncomment on Day 4

## Next: Day 3 — DDL (business table schemas)

## Day 3 — Completed

### DDL — Sequences and Business Tables
- 8 sequences created (START WITH 1000, INCREMENT BY 1, NOCACHE): `seq_customer_id`, `seq_receiver_id`, `seq_branch_id`, `seq_rider_id`, `seq_parcel_id`, `seq_history_id`, `seq_attempt_id`, `seq_fee_id`
- 8 business tables created in `cdb_admin` schema: `customers`, `receivers`, `branches`, `riders`, `parcels`, `parcel_status_history`, `delivery_attempts`, `fees`
- All scripts in `database/sql/02-schema/`, re-runnable via DROP/EXCEPTION pattern
- `run-all.sql` updated with Day 3 includes (schema section runs as `cdb_admin`)
- Verified: `SELECT table_name FROM user_tables` shows 8 business tables
- `docs/schema-design.md` written with ER diagram, column specs, and FK table

### Notes
- `parcel_status_history` ON DELETE CASCADE from parcels — trigger auto-population deferred to Day 11
- No constraints, seed data, or app code added today

## Next: Day 4 — Constraints + seed data
