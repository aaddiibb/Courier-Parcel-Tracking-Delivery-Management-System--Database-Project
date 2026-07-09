# 14-Day Prompt Plan — Courier Parcel Tracking & Delivery Management System
## Tech Stack: Laravel 11 + Oracle 11g XE + Windows/XAMPP

Day-by-day prompts for Claude Code in VSCode. Continuity is enforced by `docs/PROGRESS.md` — read at the start of every prompt, updated at the end.

---

## Three rules built into every prompt

1. **Read first, write second.** Day 2 onward starts with "read `docs/PROGRESS.md` and list relevant directories." This prevents duplicate files.
2. **Modify, don't recreate.** If a file exists, extend it.
3. **Log every day.** Every prompt ends with "update `docs/PROGRESS.md`."

If Claude Code starts drifting (creating parallel files, ignoring existing work), paste: *"Stop. Read `docs/PROGRESS.md` and list every file under `database/sql/` and `app/Http/Controllers/` before doing anything else. Then continue."*

## Lean scope targets

- **8 business tables** + Laravel's users table
- **~25 routes** (admin CRUD + parcel app + 6 lab demo pages + public tracking)
- **3 procedures, 5 functions, 4 triggers, ~40 distinct queries**
- **Thin Laravel:** raw SQL in controllers via `DB::select()` / `DB::statement()`. Eloquent only for the User model (auth). The teacher sees SQL in your controllers, not Eloquent magic.

---

## Day 1 — Environment setup (the high-risk day)

```
This is Day 1 of a 14-day courier parcel tracking project. Tech stack: Laravel 11 + Oracle 11g XE + PHP via XAMPP on Windows. No project files exist yet. My environment status: XAMPP installed at C:\xampp, Oracle 11g XE installed and running locally on this Windows machine. I'll provide passwords interactively when you ask.

Today is purely environment setup + scaffolding. Zero application features. If Day 1 fails, Day 2 cannot start.

PHASE A — Diagnose environment. Do these in order and stop after Phase A to report status:

1. `php --version` — report version.
2. `php -m` and grep for oci8 — report whether oci8 is loaded.
3. `composer --version` — report or tell me to install.
4. Run `where sqlplus` and `echo %ORACLE_HOME%` — confirm Oracle Home is set.
5. Ask me for the SYSTEM password, then `sqlplus system/<pw>@XE` and run `SELECT 1 FROM DUAL`. Confirm connection.

Report back. Wait for my "continue" before Phase B.

PHASE B — Enable OCI8 in XAMPP's PHP (skip if Phase A showed oci8 already loaded):

1. List C:\xampp\php\ext\php_oci8*.dll.
2. If a matching DLL exists: edit C:\xampp\php\php.ini, ensure exactly one uncommented `extension=oci8_12c` (or matching the DLL name without `php_` prefix and `.dll`). Tell me to restart Apache via XAMPP control panel.
3. If no DLL exists: stop and instruct me to download php_oci8 for PHP <my-version> from https://pecl.php.net/package/oci8 and place in C:\xampp\php\ext\. Wait for my confirmation.
4. After Apache restart, `php -m | findstr oci8` to verify.
5. If oci8 still won't load, the most likely cause is that Oracle's bin directory isn't on PATH. Run `echo %PATH%` and verify %ORACLE_HOME%\bin is there. If not, instruct me to add it via System Properties → Environment Variables, then restart the terminal AND Apache.

Fallback: if after 2 hours OCI8 still won't load with local Oracle XE libraries, switch to Oracle Instant Client approach — instruct me to download Instant Client Basic from oracle.com, extract to C:\instantclient, add to PATH, retry.

PHASE C — Create Laravel project:

1. Navigate to the parent directory I want the project in (ask if unsure).
2. `composer create-project laravel/laravel courier-db`
3. `cd courier-db`
4. `composer require yajra/laravel-oci8`
5. `php artisan vendor:publish --tag=oracle` (publishes config if needed)

PHASE D — Configure database connection:

1. Edit .env:
   - DB_CONNECTION=oracle
   - DB_HOST=localhost
   - DB_PORT=1521
   - DB_DATABASE=XE  (service name, not SID)
   - DB_USERNAME=system  (temporary — Day 2 switches to a dedicated schema owner)
   - DB_PASSWORD=<ask me>
2. Open config/database.php and verify the oracle connection block exists (yajra publishes it). Confirm DB_CONNECTION points to the oracle block.

PHASE E — Verify Laravel queries Oracle:

1. `php artisan tinker`
2. In tinker: `DB::connection()->getPdo()` — must return a PDO object, not an error.
3. In tinker: `DB::select('SELECT 1 + 1 AS RESULT FROM DUAL')` — must return [{"RESULT": 2}] (or similar object form).
4. Exit tinker.
5. If either fails, debug. Common causes: wrong service name (use XE not XEPDB1), ORACLE_HOME\bin missing from PATH, OCI8 not actually loaded (re-check `php -m`).

DO NOT MOVE ON UNTIL PHASE E PASSES. This is the critical gate.

PHASE F — Project structure + continuity docs:

Inside courier-db/, create these directories and files. Do NOT modify Laravel's auto-generated folders.

courier-db/
├── docs/
│   ├── PROGRESS.md
│   ├── schema-design.md
│   ├── lab-coverage.md
│   └── setup-notes.md
├── database/sql/
│   ├── 01-setup/
│   ├── 02-schema/
│   ├── 03-seed/
│   ├── 04-queries/
│   ├── 05-plsql/
│   ├── 06-procedures/
│   ├── 07-functions/
│   ├── 08-triggers/
│   ├── 09-transactions/
│   ├── 10-integration-test/
│   └── run-all.sql
└── resources/views/lab/   (new folder for lab demo pages, used Day 7+)

File contents:

- docs/PROGRESS.md — sections "Day 1 — Completed" listing every phase. Then "Next: Day 2 — Oracle users + Laravel auth scaffold."
- docs/schema-design.md — placeholder "Filled on Day 3."
- docs/lab-coverage.md — table with columns "Lab # | Topic | Planned Day | Implementation Location". Fill the first three columns now for all 12 labs (1: env setup → Day 1; 2: users & privileges → Day 2; 3: DDL → Days 3-4; 4: DML → Days 4, 5, 6; 5: transactions → Day 12; 6: constraints → Day 4; 7: aggregates → Day 8; 8: HAVING + subqueries → Day 8; 9: joins → Day 7; 10: multi-column + natural join → Day 7; 11: PL/SQL basics → Day 9; 12: control flow + procedures + functions + triggers → Days 10-11). Leave Implementation Location empty.
- docs/setup-notes.md — document exactly which OCI8 DLL is loaded, Oracle Home path, .env DB connection values (mask password), and any setup gotchas. This is the troubleshooting reference.
- database/sql/run-all.sql — header comment only. Will accumulate @-includes from Day 2 on.

End with a tree of the project. Do NOT create any schema, models, controllers, or migrations today.
```

---

## Day 2 — Oracle users, privileges, and Laravel auth

```
Continuing courier-db. Read docs/PROGRESS.md and docs/setup-notes.md before anything else. Verify the Laravel-Oracle connection from Day 1 still works: `php artisan tinker` then `DB::select('SELECT 1 FROM DUAL')`. If that fails, stop and tell me.

Day 2 covers Lab 2 (Oracle users + privileges) AND sets up Laravel's auth.

PHASE A — Oracle users (database/sql/01-setup/):

1. 01-create-users.sql — connect as SYSTEM. CREATE USER for:
   - cdb_admin (schema owner — all business tables live here)
   - cdb_branch_mgr
   - cdb_rider
   - cdb_customer
   Passwords: use simple academic-demo passwords (note in header comment they're for demo only). GRANT CREATE SESSION to all four. cdb_admin gets a tablespace quota of 100M on USERS.
   
   Wrap each CREATE USER in a PL/SQL block that catches ORA-01920 (user already exists) so the script is re-runnable.

2. 02-create-roles.sql — CREATE ROLE role_admin, role_branch_mgr, role_rider, role_customer. Same re-runnable pattern catching ORA-01921.

3. 03-grant-privileges.sql — system privileges to role_admin: CREATE TABLE, CREATE SEQUENCE, CREATE PROCEDURE, CREATE TRIGGER, CREATE VIEW, CREATE TYPE. For the other three roles, write table-level GRANT statements as COMMENTED-OUT stubs — table names will exist after Day 3, we'll uncomment then. Finally GRANT each role TO the matching user.

4. Update database/sql/run-all.sql with @-includes for these three files using relative paths from run-all.sql: `@@01-setup/01-create-users.sql` etc.

5. Run the chain: `sqlplus system/<pw>@XE @database/sql/run-all.sql`. Report errors.

6. Update .env to switch DB_USERNAME from `system` to `cdb_admin` and update DB_PASSWORD. Test via tinker: `DB::select('SELECT USER FROM DUAL')` should return CDB_ADMIN.

PHASE B — Laravel auth (Breeze):

1. `composer require laravel/breeze --dev`
2. `php artisan breeze:install blade` (choose Blade, not React/Vue, not API).
3. CRITICAL: open the generated migrations in database/migrations/. Oracle 11g has a 30-character identifier limit. Edit:
   - `create_users_table` — index names should already be short, verify.
   - `create_password_reset_tokens_table` — rename any index >30 chars (e.g. `password_reset_tokens_email_index` → `prt_email_idx`).
   - `create_sessions_table` — verify index names.
   - `create_personal_access_tokens_table` — usually has long names, shorten.
4. `php artisan migrate`. If it fails on identifier length, find the offending name in the error, edit the migration, drop any partially-created table via sqlplus, re-migrate. Document every name you shortened in setup-notes.md.
5. Add a migration to extend users with a role column: `php artisan make:migration add_role_to_users_table`. Column: `role` VARCHAR2(20) DEFAULT 'customer'. Migrate.
6. `npm install && npm run build` to compile Breeze's assets.
7. `php artisan serve` in one terminal. Visit http://127.0.0.1:8000/register in browser, create a test admin account. Manually set its role to 'admin' via sqlplus: `UPDATE users SET role='admin' WHERE email='your@email'; COMMIT;`.

PHASE C — Update docs:

- Append "Day 2 — Completed" to docs/PROGRESS.md. List: 4 Oracle users created, 4 roles created, Breeze installed, users table migrated with identifier renames documented, test admin account created. Note that table-level grants are stubbed for Day 4.
- Update docs/lab-coverage.md: Lab 2 → "database/sql/01-setup/".
- Update docs/setup-notes.md with the identifier renames done in Breeze migrations (this is reusable knowledge for any future Laravel-on-Oracle work).

Do NOT create business tables today.
```

---

## Day 3 — Business schema (Lab 3, DDL)

```
Continuing courier-db. Read docs/PROGRESS.md. Verify Oracle users exist: connect as system and run `SELECT username FROM all_users WHERE username LIKE 'CDB%'` — should return 4 rows. Verify Laravel still connects to Oracle as cdb_admin.

Day 3 creates the 8 business tables. All DDL runs as cdb_admin. Work in database/sql/02-schema/. Do NOT use Laravel migrations for these — the teacher needs to see raw DDL.

PATTERN for every table file: wrap DROP TABLE in a BEGIN/EXCEPTION block catching ORA-00942 (table doesn't exist), then CREATE TABLE. Makes scripts re-runnable.

Files (one per table for clarity):

1. 01-sequences.sql — CREATE SEQUENCE for: seq_customer_id, seq_receiver_id, seq_branch_id, seq_rider_id, seq_parcel_id, seq_history_id, seq_attempt_id, seq_fee_id. All START WITH 1000 INCREMENT BY 1 NOCACHE. Wrap each in a re-runnable block catching ORA-00955 (name in use).

2. 02-customers.sql — customer_id NUMBER PK, full_name VARCHAR2(100) NOT NULL, phone VARCHAR2(20) NOT NULL, email VARCHAR2(100), address VARCHAR2(300), created_at DATE DEFAULT SYSDATE.

3. 03-receivers.sql — receiver_id NUMBER PK, full_name VARCHAR2(100) NOT NULL, phone VARCHAR2(20) NOT NULL, address VARCHAR2(300), booking_customer_id NUMBER FK → customers(customer_id).

4. 04-branches.sql — branch_id NUMBER PK, branch_name VARCHAR2(100) UNIQUE NOT NULL, city VARCHAR2(50) NOT NULL, address VARCHAR2(200), phone VARCHAR2(20), manager_name VARCHAR2(100).

5. 05-riders.sql — rider_id NUMBER PK, full_name VARCHAR2(100) NOT NULL, phone VARCHAR2(20) NOT NULL UNIQUE, vehicle_type VARCHAR2(30), assigned_branch_id NUMBER FK → branches(branch_id), active_flag CHAR(1) DEFAULT 'Y'.

6. 06-parcels.sql — parcel_id NUMBER PK, tracking_code VARCHAR2(20) UNIQUE NOT NULL, sender_customer_id NUMBER FK → customers, receiver_id NUMBER FK → receivers, origin_branch_id NUMBER FK → branches, destination_branch_id NUMBER FK → branches, assigned_rider_id NUMBER FK → riders (nullable), weight_kg NUMBER(6,2), current_status VARCHAR2(20) DEFAULT 'BOOKED', booked_at DATE DEFAULT SYSDATE, delivered_at DATE.

7. 07-parcel-status-history.sql — history_id NUMBER PK, parcel_id NUMBER FK → parcels ON DELETE CASCADE, status VARCHAR2(20), changed_at DATE DEFAULT SYSDATE, changed_by VARCHAR2(50), remarks VARCHAR2(200). Comment that Day 11's trigger auto-populates this — do NOT create the trigger now.

8. 08-delivery-attempts.sql — attempt_id NUMBER PK, parcel_id NUMBER FK → parcels, rider_id NUMBER FK → riders, attempted_at DATE DEFAULT SYSDATE, success_flag CHAR(1), failure_reason VARCHAR2(100).

9. 09-fees.sql — fee_id NUMBER PK, parcel_id NUMBER FK → parcels UNIQUE (one fee per parcel), base_amount NUMBER(8,2), weight_charge NUMBER(8,2), total_amount NUMBER(8,2), paid_flag CHAR(1) DEFAULT 'N', paid_at DATE.

Append all nine to database/sql/run-all.sql in order. Run via `sqlplus cdb_admin/<pw>@XE @database/sql/run-all.sql`. Verify with `SELECT table_name FROM user_tables ORDER BY table_name` — should show 8 business tables.

Now write docs/schema-design.md properly. Sections: Overview, ER Diagram (ASCII art is fine), Tables (one subsection per table with column list, types, PK, FKs, defaults), Relationships (textual list of every FK).

Update docs/PROGRESS.md. Update docs/lab-coverage.md — Lab 3 → "database/sql/02-schema/".

Do NOT add constraints, seed data, or any application code today. Day 4 handles constraints + seed.
```

---

## Day 4 — Constraints + seed data (Labs 3 ALTER, 4 DML, 6 integrity)

```
Continuing courier-db. Read docs/PROGRESS.md and docs/schema-design.md. List database/sql/02-schema/ — should have 9 files. Verify all 8 business tables exist via sqlplus.

Day 4 covers Lab 3 (ALTER), Lab 4 (INSERT/UPDATE/DELETE), and Lab 6 (constraints + integrity).

PHASE A — Constraints (database/sql/02-schema/):

1. 10-alter-constraints.sql — ALTER TABLE ADD CONSTRAINT with EXPLICITLY NAMED constraints:
   - chk_parcel_status: parcels.current_status IN ('BOOKED','IN_TRANSIT','OUT_FOR_DELIVERY','DELIVERED','RETURNED')
   - chk_parcel_weight: weight_kg > 0 AND weight_kg <= 50
   - chk_parcel_branches: origin_branch_id <> destination_branch_id
   - chk_attempt_flag: delivery_attempts.success_flag IN ('Y','N')
   - chk_fee_paid: fees.paid_flag IN ('Y','N')
   - chk_fee_total: fees.total_amount >= 0
   - chk_rider_active: riders.active_flag IN ('Y','N')
   - uq_customer_phone: UNIQUE on customers.phone
   - uq_customer_email: UNIQUE on customers.email
   Every constraint NAMED explicitly. Implicit names break the report.

2. 11-alter-modify.sql — demonstrate Lab 3's ALTER MODIFY: widen customers.address to VARCHAR2(400), confirm receivers.phone has NOT NULL.

3. Revisit database/sql/01-setup/03-grant-privileges.sql. Uncomment the stubbed table grants and finalize:
   - role_branch_mgr: SELECT, INSERT, UPDATE on parcels, parcel_status_history, delivery_attempts, fees
   - role_rider: SELECT on parcels, riders; UPDATE on parcels
   - role_customer: SELECT on parcels, parcel_status_history
   Edit in place, do NOT rewrite the file.

PHASE B — Seed data (database/sql/03-seed/):

Realistic Bangladeshi data — enough volume so aggregates on Day 8 produce meaningful output.

1. 01-customers.sql — 15 customers across Dhaka, Chittagong, Sylhet, Khulna, Rajshahi. Use seq_customer_id.NEXTVAL.
2. 02-receivers.sql — 20 receivers linked to customers via booking_customer_id.
3. 03-branches.sql — 6 branches across the cities.
4. 04-riders.sql — 12 riders distributed across branches via assigned_branch_id. Mix of motorcycle/bicycle/van.
5. 05-parcels.sql — 30 parcels with status distribution: 5 BOOKED, 8 IN_TRANSIT, 4 OUT_FOR_DELIVERY, 10 DELIVERED, 3 RETURNED. Tracking codes like 'CDB202600001' through 'CDB202600030'. Weights 0.5–25 kg. DELIVERED parcels have delivered_at populated.
6. 06-parcel-status-history.sql — for each parcel, insert historical rows leading to current_status (DELIVERED parcels have 4 rows: BOOKED → IN_TRANSIT → OUT_FOR_DELIVERY → DELIVERED with sensible date progression).
7. 07-delivery-attempts.sql — 15 attempts, ~10 successful, 5 failed with varied reasons.
8. 08-fees.sql — one row per parcel. base=50, weight_charge = weight_kg * 20. Mark fees on DELIVERED parcels as paid. End with COMMIT.
9. 09-dml-demos.sql — 2 UPDATE statements + 1 DELETE, each commented with the Lab 4 operation demonstrated.

Append all 11 files (10-alter-constraints, 11-alter-modify, then 01-09 seed files) to run-all.sql in order. Run the full chain. Verify: `SELECT COUNT(*) FROM parcels` returns 30.

Update PROGRESS.md with seed row counts per table. Update lab-coverage.md for Labs 3, 4, 6.
```

---

## Day 5 — Admin CRUD pages (real Laravel app starts)

```
Continuing courier-db. Read docs/PROGRESS.md. Confirm seed data via `SELECT COUNT(*) FROM customers` (15), `SELECT COUNT(*) FROM parcels` (30).

Day 5 builds admin CRUD pages for customers, branches, riders. This proves the Laravel→Oracle stack works for actual app features. USE RAW SQL via the DB facade — NOT Eloquent. The teacher needs to see SQL in your controllers. Eloquent stays only for User/auth.

PHASE A — Controllers:

1. Generate three controllers:
   `php artisan make:controller Admin/CustomerController --resource`
   `php artisan make:controller Admin/BranchController --resource`
   `php artisan make:controller Admin/RiderController --resource`

2. Implement all 7 resource methods in each using DB::select/insert/update/delete with raw SQL. Example for CustomerController@index:
```
$customers = DB::select('SELECT customer_id, full_name, phone, email FROM customers ORDER BY full_name');
return view('admin.customers.index', compact('customers'));
```
For store: get next ID from sequence with `DB::select("SELECT seq_customer_id.NEXTVAL AS id FROM DUAL")[0]->id`, then DB::insert with that ID.

3. routes/web.php — add Route::resource for all three, grouped under middleware('auth') and prefix('admin').

PHASE B — Blade views (resources/views/admin/):

For each entity (customers, branches, riders), create:
- index.blade.php — table listing all rows
- create.blade.php — form
- edit.blade.php — form pre-filled
- show.blade.php — detail view

Use Breeze's default layout (`x-app-layout`). Tailwind classes. Functional styling only — Day 13 is polish day.

PHASE C — Navigation:

Update resources/views/layouts/navigation.blade.php — add nav links: Customers, Branches, Riders. Leave placeholders (commented) for Parcels (Day 6) and Lab Demos (Day 7).

PHASE D — Test:

1. `php artisan serve`
2. Log in with your Day 2 admin account.
3. Visit /admin/customers — should list 15 seeded customers.
4. Create a new customer, edit one, delete one. Verify changes via sqlplus.
5. Repeat for branches and riders.

If anything fails (likely: column name mismatches between SQL and views, sequence access), debug. Don't move on until all three CRUDs work end-to-end.

Update PROGRESS.md. Update lab-coverage.md — DML (Lab 4) now shown in both raw SQL files AND Laravel controllers; note both locations.
```

---

## Day 6 — Parcel booking + public tracking

```
Continuing courier-db. Read docs/PROGRESS.md. Confirm Day 5 CRUDs work by visiting /admin/customers.

Day 6 builds the core courier feature: admin parcel booking + public tracking page. Still raw SQL today. Day 10 refactors booking to call a procedure.

PHASE A — Admin parcel booking:

1. `php artisan make:controller Admin/ParcelController --resource`. Implement index, create, store, show. Skip edit/update/destroy — parcels are managed via status updates, not direct edits.

2. ParcelController@index: list parcels with joins to customers (sender), receivers, origin branch, destination branch, rider. Columns shown: tracking_code, sender name, destination city, current_status, booked_at, rider name. Raw SQL with explicit JOINs.

3. ParcelController@create: form with dropdowns populated from customers, receivers, branches (twice — for origin and destination). Weight input.

4. ParcelController@store:
   - Validate weight 0.1–50.
   - Validate origin_branch_id != destination_branch_id.
   - Get next ID via seq_parcel_id.NEXTVAL.
   - Generate tracking_code: 'CDB' || YEAR(now) || LPAD(id, 5, '0').
   - Wrap in DB::transaction([function]):
     - INSERT into parcels with current_status='BOOKED'.
     - INSERT initial parcel_status_history row.
     - Compute fee (base 50 + weight*20), INSERT into fees.
   - On success, redirect to show page with tracking_code.

5. ParcelController@show: parcel details + status history (joined to riders for the changed_by display) + delivery attempts + fee. Three sections in the view.

6. Custom route: POST /admin/parcels/{id}/update-status — handler updates current_status and inserts a history row. Day 10 moves this into a procedure; Day 11's trigger replaces the manual history insert. Don't refactor today.

7. Add "Parcels" to admin nav (uncomment Day 5's placeholder).

PHASE B — Public tracking (no login):

1. `php artisan make:controller TrackingController`.
2. routes/web.php (OUTSIDE the auth middleware group):
   - GET /track — show form
   - POST /track — look up parcel by tracking_code, render the same history view
3. resources/views/public/track.blade.php — clean form + result section showing the parcel timeline.
4. The lookup query joins parcels, customers, receivers, parcel_status_history, ordered by changed_at DESC. Render as a vertical timeline.

PHASE C — Landing page:

Edit the existing welcome.blade.php (or resources/views/welcome.blade.php) to add a prominent "Track a Parcel" input box that POSTs to /track.

PHASE D — Test:

1. As admin, book a parcel via the form. Verify all three tables got rows: parcels, parcel_status_history, fees.
2. Update its status via the POST endpoint, verify a new history row appears.
3. Open /track in an incognito window, enter the tracking code, see the timeline.

Update PROGRESS.md. EXPLICITLY note: the booking flow has manual fee insert + manual history insert. Day 10 wraps booking in a procedure. Day 11 replaces the history insert with a trigger. Do not pre-refactor today — that breaks the lab progression story.
```

---

## Day 7 — Lab demo: joins (Labs 9, 10)

```
Continuing courier-db. Read docs/PROGRESS.md. Confirm the app works: visit /admin/parcels and /track.

Day 7 starts the LAB DEMO PAGES. These are dedicated /lab/* routes whose purpose is to showcase SQL concepts to the teacher. Pattern: each page renders the SQL source code AND the result on screen. The grader can hit /lab/joins and immediately see all your JOIN work.

PATTERN for every lab demo page going forward:
- Controller in app/Http/Controllers/Lab/<Name>Controller.php
- Each query stored as an array with keys: title, explanation, sql (string), result (DB::select output)
- View iterates the array, rendering each as: heading + paragraph + <pre> code block with SQL + table of results
- Reusable Blade partial for the "demo block" so we don't duplicate markup across pages

PHASE A — Create reusable demo partial:

resources/views/lab/_demo_block.blade.php — accepts $demo variable, renders title (h3), explanation (p), SQL in a <pre> with monospace + light background, then a result table iterating $demo['result']. Use foreach to render any column dynamically (`foreach($row as $col => $val)`).

PHASE B — JoinController:

1. `php artisan make:controller Lab/JoinController`. Single index method.
2. Route in routes/web.php under auth middleware: `Route::get('/lab/joins', [JoinController::class, 'index'])->name('lab.joins')`.

Queries (each as a $demo array entry):

a) INNER JOIN — parcels with sender customer + destination branch:
   SELECT p.tracking_code, c.full_name AS sender, b.city AS destination, p.current_status
   FROM parcels p JOIN customers c ON p.sender_customer_id = c.customer_id JOIN branches b ON p.destination_branch_id = b.branch_id

b) LEFT OUTER JOIN — all branches with parcel count (include zero-parcel branches):
   SELECT b.branch_name, b.city, COUNT(p.parcel_id) AS parcel_count
   FROM branches b LEFT JOIN parcels p ON p.origin_branch_id = b.branch_id
   GROUP BY b.branch_name, b.city ORDER BY parcel_count DESC

c) RIGHT OUTER JOIN — all riders even with no parcels:
   SELECT r.full_name, COUNT(p.parcel_id) AS assigned_parcels
   FROM parcels p RIGHT JOIN riders r ON p.assigned_rider_id = r.rider_id
   GROUP BY r.full_name

d) FULL OUTER JOIN — branches FULL OUTER riders (show unassigned riders and emptyish branches):
   SELECT b.branch_name, r.full_name FROM branches b FULL OUTER JOIN riders r ON r.assigned_branch_id = b.branch_id

e) CROSS JOIN — small demo with LIMIT via ROWNUM:
   SELECT * FROM (SELECT b.branch_name, r.full_name FROM branches b CROSS JOIN riders r) WHERE ROWNUM <= 10
   Explanation should note when CROSS JOIN is useful (rare) and when it's a bug (most of the time).

f) NATURAL JOIN — pick a pair where column names align (rename via subquery aliases if needed):
   SELECT * FROM (SELECT parcel_id, current_status, weight_kg FROM parcels) NATURAL JOIN (SELECT parcel_id, total_amount FROM fees)
   Explanation should call out NATURAL JOIN's risks.

g) SELF JOIN — riders at the same branch as a specific rider:
   SELECT r1.full_name AS rider, r2.full_name AS colleague FROM riders r1 JOIN riders r2 ON r1.assigned_branch_id = r2.assigned_branch_id AND r1.rider_id <> r2.rider_id ORDER BY r1.full_name

h) Multi-column condition (Lab 10) — parcels matching multiple conditions across joined tables:
   SELECT p.tracking_code, c.full_name AS sender, p.weight_kg
   FROM parcels p JOIN customers c ON p.sender_customer_id = c.customer_id
   WHERE p.origin_branch_id <> p.destination_branch_id AND p.current_status = 'IN_TRANSIT' AND p.weight_kg > 5

PHASE C — View:

resources/views/lab/joins.blade.php — extends Breeze layout. Page header explains the demo's purpose. Then loops over the eight $demos and includes _demo_block for each. Add a Lab Demos dropdown to the nav with "Joins" linking here.

PHASE D — Test:

Visit /lab/joins. All eight queries should render with visible SQL and result tables. Verify each result count makes sense (e.g. inner join shouldn't have more rows than the parcel count).

Update PROGRESS.md. Update lab-coverage.md — Lab 9 and Lab 10 → app/Http/Controllers/Lab/JoinController.php.
```

---

## Day 8 — Lab demos: aggregates + subqueries (Labs 7, 8)

```
Continuing courier-db. Read docs/PROGRESS.md. Confirm /lab/joins renders correctly.

Day 8 adds two more lab demo pages: aggregates + subqueries. Same pattern as Day 7. Reuse the _demo_block partial.

PHASE A — AggregateController:

1. `php artisan make:controller Lab/AggregateController`. Single index. Route /lab/aggregates.

Queries:
a) COUNT GROUP BY status: SELECT current_status, COUNT(*) AS cnt FROM parcels GROUP BY current_status
b) SUM revenue per branch: SELECT b.branch_name, SUM(f.total_amount) AS revenue FROM fees f JOIN parcels p ON p.parcel_id=f.parcel_id JOIN branches b ON b.branch_id=p.origin_branch_id GROUP BY b.branch_name
c) AVG weight per branch
d) MAX and MIN weight per rider (CASE WHEN combined into one query)
e) Rider success rate via SUM(CASE WHEN success_flag='Y' THEN 1 ELSE 0 END) * 100 / COUNT(*)
f) Paid vs unpaid totals with SUM(CASE)
g) HAVING — branches with >5 parcels: GROUP BY b.branch_name HAVING COUNT(*) > 5
h) HAVING + multi-condition — riders with >2 attempts AND success rate <60%
i) HAVING — customers with >2 bookings
j) HAVING — statuses appearing >5 times in history

PHASE B — SubqueryController:

1. `php artisan make:controller Lab/SubqueryController`. Single index. Route /lab/subqueries.

Queries:
a) Non-correlated scalar — parcels heavier than overall average: WHERE weight_kg > (SELECT AVG(weight_kg) FROM parcels)
b) Non-correlated multi-row IN — riders at branches in cities with >1 branch: WHERE assigned_branch_id IN (SELECT branch_id FROM branches WHERE city IN (SELECT city FROM branches GROUP BY city HAVING COUNT(*) > 1))
c) Correlated EXISTS — customers with at least one IN_TRANSIT parcel: WHERE EXISTS (SELECT 1 FROM parcels p WHERE p.sender_customer_id = c.customer_id AND p.current_status = 'IN_TRANSIT')
d) Subquery in FROM (inline view) — derived table of per-branch counts, ranked: SELECT branch_name, parcel_count FROM (SELECT b.branch_name, COUNT(*) AS parcel_count FROM ...) WHERE parcel_count > 3
e) Scalar subquery in SELECT — for each rider, total attempts as a select column: SELECT r.full_name, (SELECT COUNT(*) FROM delivery_attempts a WHERE a.rider_id = r.rider_id) AS total_attempts FROM riders r
f) NOT EXISTS — branches that have never sent a parcel: WHERE NOT EXISTS (SELECT 1 FROM parcels WHERE origin_branch_id = b.branch_id)

PHASE C — Views + nav:

resources/views/lab/aggregates.blade.php, resources/views/lab/subqueries.blade.php — same pattern as joins. Add both to the Lab Demos nav dropdown.

PHASE D — Test:

Visit both pages. Verify each query renders SQL and produces sensible results.

Update PROGRESS.md. Update lab-coverage.md for Labs 7 and 8.
```

---

## Day 9 — PL/SQL anonymous blocks (Lab 11)

```
Continuing courier-db. Read docs/PROGRESS.md.

Day 9 covers Lab 11 — PL/SQL block structure, operators, exception handling. Anonymous blocks only today (no procedures — that's Day 10).

PHASE A — Raw SQL files (database/sql/05-plsql/):

Every script starts with `SET SERVEROUTPUT ON;`.

1. 01-block-structure.sql — three blocks:
   - Block A: declare two NUMBER variables, demonstrate arithmetic operators (+, -, *, /, MOD), print results with DBMS_OUTPUT.PUT_LINE. Add comment "Demonstrates Lab 11: arithmetic operators."
   - Block B: declare VARCHAR2 with %TYPE anchored to customers.full_name, fetch one customer via SELECT INTO, demonstrate comparison + logical operators (=, !=, >, <, AND, OR, NOT, IS NULL).
   - Block C: assignment operator (:=) demos, including %ROWTYPE for fetching a whole customer row at once.

2. 02-exception-handling.sql — three blocks:
   - Block A: SELECT INTO with WHERE clause that returns no rows → triggers NO_DATA_FOUND. Handle with named handler, print friendly message.
   - Block B: SELECT INTO that returns multiple rows → TOO_MANY_ROWS. Same pattern.
   - Block C: user-defined exception. DECLARE excessive_weight EXCEPTION; check weight from a variable; if too high, RAISE excessive_weight; handle in EXCEPTION block.

3. 03-cursor-intro.sql — explicit cursor over IN_TRANSIT parcels:
   DECLARE CURSOR c IS SELECT tracking_code, weight_kg FROM parcels WHERE current_status='IN_TRANSIT';
   v_row c%ROWTYPE;
   BEGIN OPEN c; LOOP FETCH c INTO v_row; EXIT WHEN c%NOTFOUND; DBMS_OUTPUT.PUT_LINE(...); END LOOP; CLOSE c; END;
   This bridges into Day 10's procedures.

Run all three via sqlplus, capture DBMS_OUTPUT to confirm working. Append to run-all.sql.

PHASE B — Laravel /lab/plsql page:

DBMS_OUTPUT capture from Laravel is messy. Two-step approach:

1. At the top of each .sql block in Phase A, add a line to also INSERT each output line into a small logging table (CREATE GLOBAL TEMPORARY TABLE plsql_log (block_id VARCHAR2(20), line_no NUMBER, message VARCHAR2(500)) ON COMMIT PRESERVE ROWS — created once in 05-plsql/00-logging-table.sql).

2. Replace DBMS_OUTPUT.PUT_LINE calls with both DBMS_OUTPUT.PUT_LINE AND INSERT INTO plsql_log. This way sqlplus output still works AND Laravel can SELECT from plsql_log.

3. `php artisan make:controller Lab/PlsqlController`. Index method runs each block via DB::statement (clearing plsql_log first per block), then SELECTs the log rows for display.

4. View: resources/views/lab/plsql.blade.php — for each block: title, brief explanation of which Lab 11 sub-topic, SQL source in <pre>, captured output as a list.

Alternative (simpler) if logging table approach gets complicated: hardcode the expected output in the controller as a string array, and render it. Document in setup-notes.md that the output was captured manually from sqlplus runs. Honest trade-off.

Add /lab/plsql to nav.

Update PROGRESS.md. Update lab-coverage.md for Lab 11. Document DBMS_OUTPUT capture approach in setup-notes.md.
```

---

## Day 10 — Procedures + functions (Lab 12 part 1)

```
Continuing courier-db. Read docs/PROGRESS.md and docs/schema-design.md. Verify all 8 tables + Day 9's PL/SQL scripts work.

Day 10 builds the procedures and functions. CRITICAL: this refactors Day 6's booking flow to call a procedure.

PHASE A — Procedures (database/sql/06-procedures/):

1. 01-book-parcel.sql — PROCEDURE book_parcel(
     p_sender IN NUMBER, p_receiver IN NUMBER, p_origin IN NUMBER,
     p_dest IN NUMBER, p_weight IN NUMBER, p_tracking_code OUT VARCHAR2)
   - IF weight <= 0 OR weight > 50 → RAISE_APPLICATION_ERROR(-20001, 'Invalid weight')
   - IF p_origin = p_dest → RAISE_APPLICATION_ERROR(-20002, 'Origin and destination must differ')
   - Get id from seq_parcel_id.NEXTVAL into local v_id
   - Build tracking code: 'CDB' || TO_CHAR(SYSDATE,'YYYY') || LPAD(v_id, 5, '0')
   - INSERT parcels with status='BOOKED'
   - INSERT initial parcel_status_history row
   - Call calculate_fee function (defined below), INSERT fees
   - Set p_tracking_code := built code
   - EXCEPTION block: WHEN DUP_VAL_ON_INDEX → RAISE_APPLICATION_ERROR with friendly message; WHEN OTHERS → RAISE (re-raise)
   
   Note: Day 11's trigger will replace the manual history INSERT here. Don't pre-refactor.

2. 02-assign-rider.sql — PROCEDURE assign_rider(p_parcel_id IN NUMBER, p_rider_id IN NUMBER):
   - Fetch current_status into v_status
   - CASE v_status:
     WHEN 'BOOKED' THEN UPDATE parcels SET assigned_rider_id=p_rider_id, current_status='IN_TRANSIT' WHERE parcel_id=p_parcel_id
     WHEN 'IN_TRANSIT' THEN UPDATE parcels SET assigned_rider_id=p_rider_id WHERE parcel_id=p_parcel_id
     ELSE RAISE_APPLICATION_ERROR(-20003, 'Cannot reassign rider in terminal state: ' || v_status)
   END CASE;
   - INSERT history row noting reassignment
   - EXCEPTION block for OTHERS

3. 03-update-status.sql — PROCEDURE update_parcel_status(p_parcel_id IN NUMBER, p_new_status IN VARCHAR2, p_remarks IN VARCHAR2):
   - Fetch v_old_status
   - IF-THEN-ELSIF ladder validating allowed transitions:
     - BOOKED can go to IN_TRANSIT or RETURNED
     - IN_TRANSIT can go to OUT_FOR_DELIVERY or RETURNED
     - OUT_FOR_DELIVERY can go to DELIVERED or RETURNED
     - DELIVERED and RETURNED are terminal — RAISE error
   - UPDATE parcels SET current_status = p_new_status, delivered_at = (CASE WHEN p_new_status='DELIVERED' THEN SYSDATE ELSE delivered_at END)
   - INSERT history row (Day 11 trigger will remove this)
   - EXCEPTION OTHERS

PHASE B — Functions (database/sql/07-functions/):

1. 01-calculate-fee.sql — FUNCTION calculate_fee(p_weight IN NUMBER) RETURN NUMBER:
   - IF-ELSIF tiered pricing: ≤5kg: 50+weight*20; ≤15kg: 80+weight*25; else: 150+weight*30
   - RETURN total

2. 02-get-parcel-status.sql — FUNCTION get_parcel_status(p_tracking_code IN VARCHAR2) RETURN VARCHAR2:
   - SELECT current_status INTO v_status FROM parcels WHERE tracking_code = p_tracking_code
   - EXCEPTION WHEN NO_DATA_FOUND THEN RETURN 'NOT_FOUND'

3. 03-rider-success-rate.sql — FUNCTION rider_success_rate(p_rider_id IN NUMBER) RETURN NUMBER:
   - Single query computing SUM(CASE WHEN success_flag='Y' THEN 1 ELSE 0 END) * 100 / NULLIF(COUNT(*), 0)
   - Handle zero attempts (return 0)
   - ROUND to 2 places

4. 04-customer-spend.sql — FUNCTION customer_total_spend(p_customer_id IN NUMBER) RETURN NUMBER:
   - SUM(f.total_amount) FROM fees f JOIN parcels p WHERE p.sender_customer_id = p_customer_id AND f.paid_flag = 'Y'
   - NVL handling for zero spend

5. 05-branch-revenue.sql — FUNCTION branch_revenue(p_branch_id IN NUMBER) RETURN NUMBER:
   - SUM paid fee.total_amount where p.origin_branch_id = p_branch_id

Append all 8 files to run-all.sql in this order: 01-calculate-fee FIRST (since procedures depend on it), then other functions, then procedures.

Compile all via sqlplus. Verify with: `SELECT object_name, status FROM user_objects WHERE object_type IN ('PROCEDURE','FUNCTION') ORDER BY object_type, object_name`. Status should be VALID for all.

PHASE C — Refactor ParcelController:

Edit app/Http/Controllers/Admin/ParcelController.php:
- store(): REPLACE the manual INSERT trio with a call to book_parcel. OUT parameter binding via raw PDO:
  ```php
  $pdo = DB::getPdo();
  $stmt = $pdo->prepare('BEGIN book_parcel(:s, :r, :o, :d, :w, :tc); END;');
  $stmt->bindValue(':s', $sender);
  // ... other IN params
  $stmt->bindParam(':tc', $trackingCode, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 30);
  $stmt->execute();
  // $trackingCode now contains the generated code
  ```
  Document this OUT-binding pattern in setup-notes.md.
- updateStatus(): REPLACE the manual UPDATE+history INSERT with a call to update_parcel_status.

PHASE D — Lab demo page (/lab/procedures):

1. `php artisan make:controller Lab/ProcedureController`. Route /lab/procedures.
2. Method runs each procedure/function with sample inputs (e.g. calculate_fee(7.5), rider_success_rate(1001), get_parcel_status('CDB202600001')).
3. View shows: procedure/function source code (load from the .sql file using file_get_contents), the call expression, and the result.

PHASE E — Test script:

database/sql/06-procedures/test-procedures.sql — anonymous blocks calling each procedure and function, printing results.

Update PROGRESS.md. Note: ParcelController now calls procedures instead of inline SQL for booking and status updates. Update lab-coverage.md for Lab 12 procedures+functions portion.
```

---

## Day 11 — Triggers (Lab 12 part 2)

```
Continuing courier-db. Read docs/PROGRESS.md. Verify Day 10's procedures: `SELECT object_name, status FROM user_objects WHERE object_type='PROCEDURE'`. All must be VALID.

Day 11 covers Lab 12's trigger portion. Triggers replace some manual logic from earlier days. Critical: this requires editing two procedures from Day 10.

PHASE A — Triggers (database/sql/08-triggers/):

1. 01-trg-status-history.sql — CREATE OR REPLACE TRIGGER trg_status_history AFTER UPDATE OF current_status ON parcels FOR EACH ROW WHEN (OLD.current_status != NEW.current_status):
   - INSERT INTO parcel_status_history (history_id, parcel_id, status, changed_at, changed_by, remarks) VALUES (seq_history_id.NEXTVAL, :NEW.parcel_id, :NEW.current_status, SYSDATE, USER, 'Auto-logged by trigger')

2. 02-trg-auto-return.sql — CREATE OR REPLACE TRIGGER trg_auto_return AFTER INSERT ON delivery_attempts FOR EACH ROW:
   - DECLARE v_failed_count NUMBER;
   - BEGIN IF :NEW.success_flag = 'N' THEN
       SELECT COUNT(*) INTO v_failed_count FROM delivery_attempts WHERE parcel_id = :NEW.parcel_id AND success_flag = 'N';
       IF v_failed_count >= 3 THEN UPDATE parcels SET current_status = 'RETURNED' WHERE parcel_id = :NEW.parcel_id; END IF;
     END IF; END;
   - NOTE: this trigger updates parcels, but the trigger is on delivery_attempts, so no mutating-table issue. If you accidentally write a similar trigger ON parcels that updates parcels, you'll hit ORA-04091. Comment that caveat clearly.

3. 03-trg-auto-fee.sql — CREATE OR REPLACE TRIGGER trg_auto_fee AFTER INSERT ON parcels FOR EACH ROW:
   - DECLARE v_fee NUMBER;
   - BEGIN v_fee := calculate_fee(:NEW.weight_kg);
     INSERT INTO fees (fee_id, parcel_id, base_amount, weight_charge, total_amount, paid_flag) 
     VALUES (seq_fee_id.NEXTVAL, :NEW.parcel_id, 50, :NEW.weight_kg * 20, v_fee, 'N'); END;

4. 04-trg-rider-active.sql — CREATE OR REPLACE TRIGGER trg_rider_active BEFORE INSERT OR UPDATE OF assigned_rider_id ON parcels FOR EACH ROW WHEN (NEW.assigned_rider_id IS NOT NULL):
   - DECLARE v_flag CHAR(1);
   - BEGIN SELECT active_flag INTO v_flag FROM riders WHERE rider_id = :NEW.assigned_rider_id;
     IF v_flag != 'Y' THEN RAISE_APPLICATION_ERROR(-20010, 'Cannot assign inactive rider'); END IF; END;

Compile all four via sqlplus. Verify status: `SELECT trigger_name, status FROM user_triggers`.

PHASE B — Refactor procedures to remove duplicates:

The new triggers handle what some procedures used to do manually. Edit in place:

1. database/sql/06-procedures/01-book-parcel.sql:
   - REMOVE the manual INSERT INTO fees block (trg_auto_fee handles it now).
   - KEEP the initial INSERT INTO parcel_status_history for status 'BOOKED' — trg_status_history only fires on UPDATE, not INSERT, so the initial history row still needs to be manually inserted.
   - Add a comment above the removed code pointing to trg_auto_fee.
   - Recompile the procedure.

2. database/sql/06-procedures/03-update-status.sql:
   - REMOVE the manual INSERT INTO parcel_status_history (trg_status_history handles it now).
   - Add a comment pointing to trg_status_history.
   - Recompile.

Verify procedures still compile cleanly: `SELECT object_name, status FROM user_objects WHERE object_type='PROCEDURE'`.

PHASE C — Lab demo page (/lab/triggers):

1. `php artisan make:controller Lab/TriggerController`. Route /lab/triggers.
2. Methods demonstrate each trigger firing. Use a TRANSACTION + ROLLBACK pattern so the demo doesn't pollute data:
   - Demo 1: BEGIN; UPDATE a parcel's status; SELECT new history rows; ROLLBACK.
   - Demo 2: BEGIN; INSERT 3 failed attempts on a test parcel; SELECT its current_status to show RETURNED; ROLLBACK.
   - Demo 3: BEGIN; INSERT a new parcel directly; SELECT from fees to show auto-row; ROLLBACK.
   - Demo 4: TRY to assign an inactive rider, catch the exception, display "trigger blocked it" message.
3. View shows trigger source code + the test action + result table.

PHASE D — Test script:

database/sql/08-triggers/test-triggers.sql — anonymous blocks exercising each trigger, with assertions printed via DBMS_OUTPUT.

Append all 4 trigger files to run-all.sql AFTER the procedures (triggers need functions to exist).

Update docs/PROGRESS.md. EXPLICITLY note the two procedure edits in Phase B so Day 13's audit doesn't re-add removed code. Update lab-coverage.md for triggers (Lab 12).
```

---

## Day 12 — Transactions (Lab 5)

```
Continuing courier-db. Read docs/PROGRESS.md.

Day 12 covers Lab 5 — COMMIT, ROLLBACK, SAVEPOINT. Demonstrated in both raw SQL and Laravel.

PHASE A — Raw SQL (database/sql/09-transactions/):

1. 01-success-transaction.sql — anonymous block:
   - Insert a test customer (capture id)
   - Insert a test receiver linked to that customer
   - Call book_parcel
   - COMMIT
   - SELECT to confirm all rows persisted
   - Comment each statement labeling the Lab 5 concept

2. 02-rollback.sql — block:
   - Insert a test customer
   - Try book_parcel with origin = destination (will fail the constraint inside the procedure)
   - Trigger EXCEPTION handler: ROLLBACK
   - SELECT to confirm customer insert was rolled back too (because COMMIT hadn't happened yet)

3. 03-savepoint.sql — multi-step:
   - INSERT customer; SAVEPOINT sp1
   - INSERT receiver; SAVEPOINT sp2
   - Try INSERT a parcel with invalid weight (fails chk_parcel_weight)
   - ROLLBACK TO sp2  -- customer + receiver persist, parcel doesn't
   - COMMIT
   - SELECT to verify exactly two rows persisted, no parcel

Append all three to run-all.sql. Run via sqlplus, verify behavior.

PHASE B — Laravel transactions:

1. Re-verify ParcelController@store still wraps in DB::transaction. (Day 6 added this; Day 10 refactored to use the procedure inside the closure. Confirm it still works.)

2. `php artisan make:controller Lab/TransactionController` with three routes:
   - GET /lab/transactions/success — runs a 3-step transaction that succeeds, shows persisted rows. Use DB::transaction(function() {...}).
   - GET /lab/transactions/rollback — runs a transaction that throws partway through, shows nothing persisted. Wrap in try/catch around DB::transaction to display the caught exception.
   - GET /lab/transactions/savepoint — uses manual DB::beginTransaction(); DB::statement('SAVEPOINT sp1'); ... DB::statement('ROLLBACK TO sp1'); DB::commit();

3. resources/views/lab/transactions.blade.php — three sections (one per scenario), each showing the controller code, the SQL operations, and the final row counts before/after.

4. Add to nav: Lab Demos → Transactions.

PHASE C — Day 12 audit:

Now do a full project sanity audit:
1. Run `ls -R` (or `tree`) on the project, save output.
2. Read docs/PROGRESS.md cover to cover.
3. For every file mentioned in PROGRESS.md, verify it exists.
4. For every file under database/sql/, verify it's referenced in run-all.sql.
5. For every controller in app/Http/Controllers/, verify it has a route in routes/web.php.
6. For every view in resources/views/admin/ and resources/views/lab/, verify a controller renders it.
7. Run `sqlplus cdb_admin/<pw>@XE @database/sql/run-all.sql` against a fresh schema (drop user objects first if needed). Confirm clean execution from start to finish.
8. Append a "Day 12 Audit" section to PROGRESS.md listing every drift: missing files, files not in run-all.sql, orphaned controllers, views with no controllers, broken queries. DO NOT FIX TODAY — Day 13 is fix day.

Update lab-coverage.md for Lab 5.
```

---

## Day 13 — Polish + fix audit issues

```
Continuing courier-db. Read docs/PROGRESS.md INCLUDING the Day 12 audit list.

Day 13 fixes audit findings and adds frontend polish. No new features beyond polish.

PHASE A — Fix audit items:

Go through the Day 12 audit list. For each item:
- Fix it in place (don't recreate files).
- Add a line to the "Day 13 Fixes" section of PROGRESS.md.

Common fixes likely to appear:
- A controller without a route → add route OR delete the unused controller method
- A view without a controller → wire it up OR delete
- A .sql file not in run-all.sql → add @-include
- A query referencing a wrong column name → fix
- A trigger or procedure with INVALID status → recompile, debug

PHASE B — Frontend polish:

1. Parcel listing (resources/views/admin/parcels/index.blade.php) — add sort/filter/search:
   - Search box: text input that filters by tracking_code LIKE %...% via server-side raw SQL
   - Status filter dropdown (BOOKED, IN_TRANSIT, etc.) — adds WHERE current_status = ?
   - Origin branch dropdown filter
   - Sortable columns: clickable headers that re-request with ?sort=column&dir=asc/desc
   - Server-side pagination using Oracle 11g ROWNUM technique:
     ```sql
     SELECT * FROM (
       SELECT a.*, ROWNUM rnum FROM (
         <inner query with ORDER BY>
       ) a WHERE ROWNUM <= :end
     ) WHERE rnum > :start
     ```
   - 10 results per page
   - All four (search, filter, sort, paginate) must work together in one query

2. Apply Tailwind styling consistently:
   - Shared button classes (primary, secondary, danger)
   - Consistent table styling
   - Form inputs with proper labels and validation message areas
   - Card layouts for the dashboard

3. Dashboard page (/admin/dashboard or just /) for logged-in admins:
   - 4 summary cards: total parcels, in-transit count, today's revenue, active riders
   - Mini table of recent parcels (last 5)
   - Each card backed by an aggregate query (reuse from Day 8 where applicable)

4. Public landing page (/) for not-logged-in:
   - Hero with tracking input that POSTs to /track
   - Brief feature description
   - Login/register links

PHASE C — End-to-end integration test:

1. database/sql/10-integration-test/01-smoke-test.sql — anonymous block:
   - Note start: DBMS_OUTPUT.PUT_LINE('=== Smoke test start ===')
   - Call book_parcel for a brand new parcel (capture tracking code)
   - Call assign_rider
   - Call update_parcel_status to walk through: BOOKED → IN_TRANSIT → OUT_FOR_DELIVERY → DELIVERED
   - SELECT COUNT(*) FROM parcel_status_history WHERE parcel_id = v_test_id — must be 4 (one initial + three triggered)
   - Print PASS/FAIL
   - Insert 3 failed delivery_attempts on a different test parcel
   - SELECT current_status from that parcel — must be 'RETURNED'
   - Print PASS/FAIL
   - SELECT total_amount FROM fees for the first test parcel — verify it matches calculate_fee logic
   - Print PASS/FAIL
   - ROLLBACK at the end so the test doesn't pollute seed data
   - DBMS_OUTPUT.PUT_LINE('=== Smoke test end ===')

2. Append to run-all.sql as the final entry.

3. Run the entire chain end to end. Confirm everything works and the smoke test prints all PASS messages.

Update docs/PROGRESS.md with the Day 13 Fixes section listing each item resolved + a "Known Limitations" section for anything left unfixed.

Do NOT add new database objects or controller methods today. The project is feature-complete after polish.
```

---

## Day 14 — Final report + demo script

```
Continuing courier-db. Read docs/PROGRESS.md, docs/schema-design.md, docs/lab-coverage.md, docs/setup-notes.md. List the full project tree.

Day 14 is the report. Work in docs/ — no code changes today.

PHASE A — docs/final-report.md:

Sections in order:
1. Title page placeholder (Project Title, Student Name, ID, Course, Date — leave fillable).
2. Abstract — one paragraph: what the system is, what it manages, key tech, what lab topics are demonstrated.
3. Introduction & Problem Statement — 2 paragraphs.
4. System Features — bullet list (customer management, receiver management, parcel booking with tracking codes, branch + rider assignment, 5-state status tracking, failed attempt tracking, fee calculation, public tracking page, admin dashboards, lab demo pages).
5. Tech Stack — Laravel 11, Oracle 11g XE, OCI8 driver, yajra/laravel-oci8 v11, Laravel Breeze for auth, Blade + Tailwind for UI, SQL*Plus for direct DB access.
6. Database Design — paste docs/schema-design.md content. Include the ASCII ER diagram.
7. Implementation Walkthrough — one paragraph per major area:
   - Oracle users & privileges (database/sql/01-setup/)
   - Schema (database/sql/02-schema/)
   - Seed data (database/sql/03-seed/)
   - Auth & user management (Laravel Breeze, app/Models/User.php)
   - Admin CRUD (app/Http/Controllers/Admin/)
   - Parcel booking & tracking (app/Http/Controllers/Admin/ParcelController.php, TrackingController.php)
   - Lab demo pages (app/Http/Controllers/Lab/)
   - Procedures (database/sql/06-procedures/)
   - Functions (database/sql/07-functions/)
   - Triggers (database/sql/08-triggers/)
   - Transactions (database/sql/09-transactions/)
   Each paragraph: what it does, key file paths, which lab topics it shows.

8. Lab Coverage Matrix — full table with columns: Lab # | Topic | File Path | Brief Description. Fill from lab-coverage.md with concrete paths. Should have at least one row per lab.

9. Sample Outputs — pick 5 strongest queries from the /lab/* pages. For each: the SQL, an explanation of what it answers, and an ASCII representation of the result. Add "[Screenshot]" placeholders for screenshots you'll add manually before submission.

10. Testing — describe database/sql/10-integration-test/01-smoke-test.sql: what it verifies, what PASS messages look like.

11. Limitations & Future Work — one paragraph each. Honest about what isn't perfect (e.g. DBMS_OUTPUT capture hack, manual screenshot insertion).

12. Conclusion — one paragraph.

PHASE B — docs/demo-script.md:

10-minute live demo for the teacher. Sections:
1. Setup (1 min) — open VSCode, show project tree, explain separation of business app (/admin/*, /track) vs lab demo pages (/lab/*).
2. Database tour (2 min) — open sqlplus, run: `SELECT table_name FROM user_tables`, `SELECT object_name, object_type FROM user_objects WHERE object_type IN ('PROCEDURE','FUNCTION','TRIGGER','SEQUENCE') ORDER BY object_type`. Mention counts.
3. App walkthrough (3 min) — login as admin, walk customers → branches → riders → parcels. Book a new parcel live. Open the public tracking page in another tab with the new tracking code.
4. Lab demos (3 min) — visit /lab/joins (show inner, outer, self), /lab/aggregates (show GROUP BY + HAVING), /lab/subqueries (show correlated EXISTS), /lab/plsql, /lab/triggers (run the auto-return demo live), /lab/transactions (run the rollback demo).
5. Procedure + trigger demo from sqlplus (1 min) — call book_parcel from sqlplus with EXEC, then SELECT from parcel_status_history to show the trigger's auto-row.

Time each section. Note which queries are strongest to highlight if running short on time.

PHASE C — Verify lab coverage matrix:

Re-read docs/lab-coverage.md. Every one of the 12 labs MUST have at least one file reference. If any lab is missing a concrete location, explicitly state that in final-report.md's Limitations section. Do NOT hide gaps.

PHASE D — Update top-level README.md:

- Project name + one-line description
- Quick screenshot placeholder
- Setup instructions referencing docs/setup-notes.md
- Run instructions: `php artisan serve`, default URL
- Route map (admin/, /track, /lab/*)
- Credits

PHASE E — Final PROGRESS.md update:

- Mark project complete
- Total file count: `find . -type f \( -name "*.php" -o -name "*.sql" -o -name "*.blade.php" -o -name "*.md" \) | wc -l`
- Total LOC: similar find piped to wc -l
- Date and final notes

PHASE F — Print final project tree.

Project complete. Do not modify any code files unless something is genuinely blocking the report.
```

---

## Operational tips

- **Commit to git every day.** `git commit -m "Day N: <one-line summary>"`. Commit timestamps are the strongest evidence of incremental work.
- **Save sqlplus output to `logs/`** after each day's scripts run. These go into the final report's sample-outputs section.
- **Don't paste multiple days at once.** PROGRESS.md continuity only works with one day per session.
- **If Claude Code drifts, the recovery line is:** *"Stop. Read `docs/PROGRESS.md` and list every file under `database/sql/` and `app/Http/Controllers/` before doing anything else. Then continue."*
- **Day 1 is the highest risk.** Budget extra time. If OCI8 won't load after 2 hours, switch to the Instant Client fallback documented in the Day 1 prompt.
- **Don't let Claude Code skip ahead.** If it offers to "also add Day 8's queries while we're at it," decline. The daily pace is the whole point.

## Lab coverage cheat sheet (target end state)

| Lab | Topic | Location |
|---|---|---|
| 1 | Environment setup | docs/setup-notes.md |
| 2 | Users & privileges | database/sql/01-setup/ |
| 3 | DDL (CREATE, ALTER, DROP) | database/sql/02-schema/ |
| 4 | DML (INSERT, UPDATE, DELETE, SELECT) | database/sql/03-seed/ + all Laravel controllers |
| 5 | Transactions (COMMIT, ROLLBACK) | database/sql/09-transactions/ + Lab/TransactionController |
| 6 | Constraints & integrity | database/sql/02-schema/10-alter-constraints.sql |
| 7 | Aggregates & GROUP BY | Lab/AggregateController |
| 8 | HAVING + subqueries | Lab/AggregateController + Lab/SubqueryController |
| 9 | Joins (inner, outer, cross) | Lab/JoinController |
| 10 | Multi-column conditions + NATURAL JOIN | Lab/JoinController |
| 11 | PL/SQL basics | database/sql/05-plsql/ + Lab/PlsqlController |
| 12 | Control flow + procedures + functions + triggers | database/sql/06-procedures/, 07-functions/, 08-triggers/ + Lab/ProcedureController + Lab/TriggerController |
