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

## Day 4 — Completed

### Phase A — Constraints & Column Modifications
- 9 named CHECK/UNIQUE constraints added via `02-schema/10-alter-constraints.sql` (re-runnable):
  - `chk_parcel_status`, `chk_parcel_weight`, `chk_parcel_branches`
  - `chk_attempt_flag`, `chk_fee_paid`, `chk_fee_total`, `chk_rider_active`
  - `uq_customer_phone`, `uq_customer_email`
- `02-schema/11-alter-modify.sql`: widened `customers.address` → VARCHAR2(400); confirmed `receivers.phone` NOT NULL
- `01-setup/03-grant-privileges.sql`: stubs replaced with correct table-level grants for `role_branch_mgr`, `role_rider`, `role_customer`

### Phase B — Seed Data
| Table                  | Rows |
|------------------------|------|
| customers              |   15 |
| receivers              |   20 |
| branches               |    6 |
| riders                 |   12 |
| parcels                |   30 |
| parcel_status_history  |   85 |
| delivery_attempts      |   14 |
| fees                   |   30 |

- Parcel distribution: 5 BOOKED, 8 IN_TRANSIT, 4 OUT_FOR_DELIVERY, 10 DELIVERED, 3 RETURNED
- `delivery_attempts` = 14 (15 inserted, 1 deleted by DML demo in 09-dml-demos.sql)
- `09-dml-demos.sql`: Lab 4 demo — UPDATE parcels status, UPDATE fees paid_flag, DELETE failed attempt

### Verification
- `SELECT COUNT(*) FROM parcels` → **30** ✓
- All 11 new files appended to `run-all.sql` in correct order

## Day 5 — Completed

### Phase A — Admin Controllers + Routes
- 3 resource controllers generated: `Admin/CustomerController`, `Admin/BranchController`, `Admin/RiderController`
- All 7 resource methods implemented in each using `DB::select/insert/update/delete` with raw SQL (no Eloquent)
- `store` methods fetch next PK via `seq_*_id.NEXTVAL` from DUAL, then pass it into INSERT
- `RiderController@index` uses LEFT JOIN to resolve `assigned_branch_id` → `branch_name`
- `RiderController@create/edit` pass `$branches` to the view for the dropdown
- `routes/web.php`: 3 `Route::resource` calls under `middleware('auth')->prefix('admin')->name('admin.')`
- `php artisan route:list --path=admin` → 21 routes confirmed (7 × 3)

### Phase B — Blade Views
- 12 blade views created under `resources/views/admin/` (4 per entity: index, create, edit, show)
- All use `x-app-layout` with Tailwind; index views have flash success banners and inline delete forms with confirm dialogs
- Riders index shows active_flag as green/red badge; edit pre-selects vehicle type, branch, and active flag via `old()`
- Column names accessed as lowercase properties (yajra/oci8 driver lowercases Oracle column names)

### Phase C — Navigation
- `resources/views/layouts/navigation.blade.php` updated with Customers, Branches, Riders links (desktop + responsive)
- Placeholders commented for Parcels (Day 6) and Lab Demos (Day 7)

## Day 6 — Completed

### Phase A — Admin Parcel Booking
- `Admin/ParcelController` generated with index, create, store, show (edit/update/destroy skipped — parcels are managed via status updates)
- `index`: raw SQL with JOINs to customers, receivers, branches (origin + destination), riders — shows tracking_code, sender, destination city, status, booked_at, rider
- `create`: dropdowns populated from customers, receivers, branches (×2), active riders
- `store`: validates weight (0.1–50) and origin ≠ destination; gets NEXTVAL; runs DB::transaction wrapping:
  1. INSERT into parcels with status='BOOKED'
  2. INSERT initial parcel_status_history row (changed_by = auth user name)
  3. Computes fee (base 50 + weight×20), INSERT into fees
  **NOTE: manual history insert + manual fee insert — Day 10 wraps booking in a stored procedure; Day 11's trigger replaces the manual history insert. Not pre-refactored today.**
- `show`: parcel details + fee breakdown + status history + delivery attempts (three sections)
- Custom route `POST /admin/parcels/{id}/update-status` → `updateStatus()`: updates current_status, sets delivered_at if DELIVERED, inserts history row. **Day 10 moves this into a procedure; Day 11 replaces the history insert with a trigger.**
- "Parcels" link added to nav (desktop + responsive)

### Phase B — Public Tracking
- `TrackingController` with `index` (GET /track) and `track` (POST /track)
- Both routes outside auth middleware
- `resources/views/public/track.blade.php`: standalone page (no Breeze layout), search form + result summary card + vertical timeline of status history

### Phase C — Landing Page
- `resources/views/welcome.blade.php` updated with prominent "Track a Parcel" box (indigo banner) that POSTs to /track

## Day 7 — Completed

### Phase A — Reusable Lab Partial
- `resources/views/lab/_demo_block.blade.php` created — accepts `$demo` array (title, explanation, sql, result); renders h3 + p + dark `<pre>` SQL block + dynamic result table with row count badge

### Phase B — JoinController
- `app/Http/Controllers/Lab/JoinController.php` — single `index()` method; builds `$demos` array with 8 entries
- Route added: `GET /lab/joins` → `lab.joins`, under `auth` middleware

### Queries implemented (a–h)
| # | Type               | Result rows | Notes |
|---|--------------------|-------------|-------|
| a | INNER JOIN         | 30          | Parcels × customers × branches |
| b | LEFT OUTER JOIN    | 6           | All branches including zero-parcel ones |
| c | RIGHT OUTER JOIN   | 12          | All 12 riders including unassigned |
| d | FULL OUTER JOIN    | 12          | All branch-rider assignments |
| e | CROSS JOIN         | 10          | Cartesian product, ROWNUM-limited |
| f | NATURAL JOIN       | 30          | parcels × fees on parcel_id |
| g | SELF JOIN          | 12          | Rider colleagues at same branch |
| h | Multi-column (Lab 10) | 3        | IN_TRANSIT, weight > 5, cross-branch |

### Phase C — View
- `resources/views/lab/joins.blade.php` — extends `x-app-layout`, purpose banner, loops `$demos` via `@include('lab._demo_block')`
- Nav updated: "Lab Demos" dropdown (desktop) + responsive section; "Joins (Labs 9 & 10)" links to `/lab/joins`

## Day 8 — Completed

### Phase A — AggregateController
- `app/Http/Controllers/Lab/AggregateController.php` — single `index()` with 10 demo queries
- Route added: `GET /lab/aggregates` → `lab.aggregates`

| # | Query type | Highlight |
|---|------------|-----------|
| a | COUNT + GROUP BY | Parcel count per status |
| b | SUM + multi-join | Revenue per origin branch (fees → parcels → branches) |
| c | AVG | Average weight per branch |
| d | MAX + MIN combined | Weight range per assigned rider |
| e | SUM(CASE WHEN) | Rider delivery success rate % |
| f | Pivot SUM(CASE) | Paid vs unpaid totals in a single row |
| g | HAVING | Branches with > 5 outgoing parcels |
| h | HAVING multi-condition | Riders with > 2 attempts AND success rate < 60% |
| i | HAVING | Customers with > 2 bookings |
| j | HAVING | Status transitions appearing > 5 times in history |

### Phase B — SubqueryController
- `app/Http/Controllers/Lab/SubqueryController.php` — single `index()` with 6 demo queries
- Route added: `GET /lab/subqueries` → `lab.subqueries`

| # | Subquery type | Highlight |
|---|---------------|-----------|
| a | Non-correlated scalar | Parcels heavier than overall AVG |
| b | Non-correlated IN (nested) | Riders in cities with > 1 branch |
| c | Correlated EXISTS | Customers with ≥ 1 IN_TRANSIT parcel |
| d | Inline view (FROM subquery) | Branch parcel counts filtered by derived column |
| e | Scalar subquery in SELECT | Total attempts per rider as a select column |
| f | NOT EXISTS | Branches that have never sent a parcel |

### Phase C — Views + Nav
- `resources/views/lab/aggregates.blade.php` — amber banner, loops `$demos` via `_demo_block` partial
- `resources/views/lab/subqueries.blade.php` — emerald banner, same pattern
- Nav dropdown updated: "Aggregates & HAVING (Labs 11 & 12)" and "Subqueries (Labs 13 & 14)" added (desktop + responsive)

## Day 9 — Completed

### Phase A — PL/SQL Anonymous Blocks (database/sql/05-plsql/)
- `00-logging-table.sql`: `CREATE GLOBAL TEMPORARY TABLE plsql_log (block_id, line_no, message) ON COMMIT PRESERVE ROWS` — re-runnable; used by Laravel to capture DBMS_OUTPUT
- `01-block-structure.sql` — three blocks:
  - Block A: two NUMBER variables, all five arithmetic operators (+, -, *, /, MOD), DBMS_OUTPUT.PUT_LINE
  - Block B: VARCHAR2/phone/email variables anchored with `%TYPE` to `customers` columns; SELECT INTO first customer; demonstrates =, !=, AND, OR, NOT, IS NULL, IS NOT NULL
  - Block C: `customers%ROWTYPE` fetches whole row; `:=` assignment operator; `NVL()` for NULL handling
- `02-exception-handling.sql` — three blocks:
  - Block A: `SELECT INTO WHERE customer_id = -999` → `NO_DATA_FOUND` caught by named handler
  - Block B: `SELECT full_name INTO v_name FROM customers` (no WHERE) → `TOO_MANY_ROWS` caught
  - Block C: user-defined `excessive_weight EXCEPTION`; `RAISE` when v_weight > 50; handled in EXCEPTION section
- `03-cursor-intro.sql`: explicit cursor over `IN_TRANSIT` parcels — `OPEN c / LOOP / FETCH c INTO v_row / EXIT WHEN c%NOTFOUND / CLOSE c`; prints `c%ROWCOUNT` summary; bridges into Day 10 procedures
- All four files appended to `run-all.sql` under "Day 9"

### Phase B — Laravel /lab/plsql
- `PlsqlController` (`app/Http/Controllers/Lab/PlsqlController.php`): `index()` runs all 6 anonymous blocks + cursor via `DB::statement()`, reads back output from `plsql_log` via `DB::select()`, wraps each block in try/catch
- Route added: `GET /lab/plsql` → `lab.plsql`
- View: `resources/views/lab/plsql.blade.php` — violet banner, 7 blocks (BS-A, BS-B, BS-C, EX-A, EX-B, EX-C, CUR-A) each showing: sub-topic badge, explanation, clean PL/SQL source in `<pre>`, live DBMS_OUTPUT lines rendered as an amber terminal list; error state if plsql_log table missing
- Nav: "PL/SQL Basics (Lab 11)" added to "Lab Demos" dropdown (desktop + responsive)

### Notes
- DBMS_OUTPUT cannot be read from PHP/OCI8 — output is captured via `plsql_log` GTT (each block deletes its own rows then re-inserts on each run)
- Exception blocks handle their own logging inside the EXCEPTION handler so the caught message is always written
- No stored procedures today — that is Day 10

## Day 10 — Role-Based Access Control Foundation (2026-06-30)

### DB column verified
- `USERS.ROLE` confirmed as `VARCHAR2` via `user_tab_columns`:
  `DB::select("SELECT column_name, data_type FROM user_tab_columns WHERE table_name='USERS'")`

### PHASE A — Reusable role middleware
- Created `app/Http/Middleware/EnsureUserHasRole.php`
  - Variadic: `route:admin`, `route:admin,branch_mgr`, etc.
  - Unauthenticated → redirect to `login`
  - Wrong role → `abort(403, 'You do not have access to this section.')`
- Registered `role` alias in `bootstrap/app.php` alongside legacy `admin`/`customer` aliases

### PHASE B — Smart login redirect
- Created `app/Support/RoleRedirect::path($role)` — single source of truth:
  - `admin` → `/admin/dashboard`
  - `branch_mgr` → `/branch/dashboard`
  - `rider` → `/rider/dashboard`
  - `customer` (default) → `/customer/dashboard`
- `AuthenticatedSessionController::store()` now calls `RoleRedirect::path()`
- Added `public static redirectPathForRole($role)` proxy on the controller for external use
- Old `/dashboard` route kept as backward-compat smart redirect

### PHASE C — Route reorganization
- `routes/web.php` restructured into commented role groups using `role:xxx` middleware:
  - `admin.*` → `middleware(['auth','role:admin'])`, prefix `admin/`
  - `branch.*` → `middleware(['auth','role:branch_mgr'])`, prefix `branch/`
  - `rider.*` → `middleware(['auth','role:rider'])`, prefix `rider/`
  - `customer.*` → `middleware(['auth','role:customer'])`, prefix `customer/`
- All Day 5/6 admin CRUD routes preserved; reports/operations moved inside admin group
- Stub controllers + placeholder views created:
  - `app/Http/Controllers/Branch/DashboardController` → `branch.dashboard`
  - `app/Http/Controllers/Rider/DashboardController` → `rider.dashboard`
  - `resources/views/branch/dashboard.blade.php` — placeholder
  - `resources/views/rider/dashboard.blade.php` — placeholder
- Navigation updated: branches on `$role` for all 4 roles with role-coloured badges
  (Admin=indigo, Branch Mgr=yellow, Rider=orange, Customer=green)
- `errors/access-denied.blade.php` updated to use `RoleRedirect::path()` for the back-link

### PHASE D — Test accounts (password: `password`)
| Email               | Role        |
|---------------------|-------------|
| admin@test.com      | admin       |
| branchmgr@test.com  | branch_mgr  |
| rider@test.com      | rider       |
| customer@test.com   | customer    |

### Follow-up (future day) — Branch/Rider user linking
- `branches` and `riders` tables have **no `user_id` FK** column.
- Until that FK is added, branch_mgr and rider accounts cannot be linked to their
  Oracle rows. Future migration: add `user_id NUMBER REFERENCES users(id)` to both
  tables and back-fill during account creation.

### Route verification
`php artisan route:list --name=dashboard` confirms 5 routes:
`dashboard`, `admin.dashboard`, `branch.dashboard`, `rider.dashboard`, `customer.dashboard`

## Day 11 — Admin Dashboard UI (2026-06-30)

### PHASE A — Admin sidebar layout
- Created `resources/views/components/admin-layout.blade.php` — anonymous Blade component
  resolved as `<x-admin-layout>`
  - Dark sidebar (`bg-gray-900`, `w-64`, fixed): brand link, nav items, Lab Demos accordion, user/logout footer
  - Nav items with active-link highlighting via `request()->routeIs()`:
    Dashboard · Customers · Branches · Riders · Parcels
  - Lab Demos accordion (Alpine.js, `x-show`/`x-cloak`): Joins, Aggregates, Subqueries, PL/SQL Basics
    — links to `/lab/*` paths (controllers not yet rebuilt; will 404 until restored)
  - Main content: `ml-64`, optional `$header` slot rendered in white shadow bar above `$slot`
  - `[x-cloak]` CSS rule in `<head>` prevents Alpine FOUC

### PHASE B — Dashboard controller + view
- Created `app/Http/Controllers/Admin/DashboardController@index`
  Five raw-SQL queries (DB::select, no Eloquent):
  1. `COUNT(*) FROM parcels` → `$totalParcels`
  2. `GROUP BY current_status` → `$byStatus` (also extracts `$inTransit`)
  3. `NVL(SUM(total_amount),0) WHERE paid_flag='Y' AND TRUNC(paid_at)=TRUNC(SYSDATE)` → `$todaysRevenue`
  4. `COUNT(*) WHERE active_flag='Y'` → `$activeRiders`
  5. Last 5 parcels joined to customers + branches → `$recentParcels`
- Updated `routes/web.php` admin.dashboard to use `DashboardController@index`
- Created `resources/views/admin/dashboard.blade.php` using `<x-admin-layout>`:
  - 4 stat cards (Total Parcels, In Transit, Today's Revenue, Active Riders) — plain bordered white boxes
  - Parcels-by-status table with colour-coded status badges
  - Recent Parcels table (5 rows, tracking code links to parcel show page)

### PHASE C — Admin CRUD pages migrated to sidebar layout
All 15 existing admin views switched from `<x-app-layout>` → `<x-admin-layout>`:
- customers: index, create, edit, show
- branches: index, create, edit, show
- riders: index, create, edit, show
- parcels: index, create, show

### Query results (live Oracle seeded data)
| Metric | Value |
|--------|-------|
| total_parcels | 30 |
| DELIVERED | 10 |
| IN_TRANSIT | 7 |
| BOOKED | 5 |
| OUT_FOR_DELIVERY | 5 |
| RETURNED | 3 |
| active_riders | 11 |
| today's revenue | 0 (seed dates are historical) |

### Follow-up — Lab demo routes
Lab controllers (`JoinController`, `AggregateController`, `SubqueryController`, `PlsqlController`)
and their views were not present in the current branch. Sidebar links exist at `/lab/*` but will
return 404 until the controllers and views are restored (noted as a future-day task).

## Day 12 — Customer Dashboard + Final RBAC Audit (2026-07-01)

### Pre-flight findings (before writing any code)
- `rider.dashboard` and `branch.dashboard` are **still stub placeholders** —
  `Rider\DashboardController@index` / `Branch\DashboardController@index` just
  `return view(...)` with no queries, and the views say "tools coming soon."
  Neither `riders` nor `branches` has a `user_id` column, so there is no way to
  scope either role to "their own" data yet. This contradicts the "last of the
  four role dashboards" framing — only **admin** has a real dashboard so far;
  branch_mgr/rider dashboards are future-day work, not done today.
- The customer dashboard/parcel views already existed in the working tree
  (uncommitted) from a previous session, but their scoping matched
  `customers.email = auth()->user()->email`. `customer@test.com` had **no**
  matching row in `customers`, and `customers` had no `user_id` column at all
  — so the "existing" dashboard silently showed the empty/unlinked state for
  the designated test account. Phase A below fixes this properly.
- `database/sql/run-all.sql` was deleted in the working tree; restored and
  appended with the Day 10 table-grants file and today's new schema file.

### PHASE A — Customer account linking
- `database/sql/02-schema/13-alter-customers-user-link.sql`: adds
  `customers.user_id NUMBER`, `fk_customers_user` FK → `users(id)`,
  `uq_customers_user` UNIQUE (one Laravel login per customer, re-runnable).
- Ran via sqlplus as `cdb_admin`; linked `customer@test.com` (users.id=45) to
  `customer_id=1000` (Karim Uddin Ahmed) via `UPDATE customers SET user_id = 45
  WHERE customer_id = 1000`.
- Documented in `docs/schema-design.md` (customers table + relationships table).
- `app/Support/CustomerContext.php` — single place that resolves
  `customers` row / `customer_id` for the current `auth()->user()`, used by
  every Customer controller instead of ad-hoc email lookups.

### PHASE B — Customer layout
- `resources/views/components/customer-layout.blade.php` — sidebar layout
  (`<x-customer-layout>`), matching the `<x-admin-layout>` component pattern
  but visually distinct: white sidebar, orange accent, emoji nav icons,
  warmer consumer tone vs. the dark internal-tool admin sidebar.
- Nav: My Parcels (dashboard), All Shipments, Book a Parcel, My Addresses, Track.

### PHASE C — Dashboard
- `Customer\DashboardController@index` rewritten to use `CustomerContext`;
  added a "Total Spend" stat (`SUM(fees.total_amount) WHERE paid_flag='Y'`,
  joined through the customer's own parcels).
- **Bug fixed**: the existing recent-parcels query used
  `FETCH FIRST 5 ROWS ONLY`, which is Oracle 12c+ syntax — this XE 11g
  instance doesn't support it (ORA-00933). Rewrote as a `ROWNUM`-limited
  inline view, matching the pattern used elsewhere in the codebase.
- `resources/views/customer/dashboard.blade.php` updated to the new layout
  + stat card.

### PHASE D — Parcels (view + book)
- `Customer\ParcelController` rewritten: `index`/`show` scoped via
  `CustomerContext`; `show` explicitly compares `parcel->sender_customer_id`
  against the caller's own `customer_id` and `abort(403)`s on mismatch —
  role middleware alone does not prove ownership of a specific parcel.
- Extracted `resources/views/partials/parcel-timeline.blade.php` from the
  customer parcel-show timeline (the richer of the two near-duplicate
  implementations) and reused it from both `/track` (public) and
  `/customer/parcels/{tracking_code}` (authenticated) — single source of
  truth for status-history rendering.
- **Bug fixed**: `:by` is not a valid Oracle bind variable name in this
  OCI8 driver (ORA-01745, "invalid host/bind variable name") — it was
  silently breaking every parcel-status-history insert that used it,
  including the pre-existing `Admin\ParcelController@store` and
  `@updateStatus`. Renamed to `:changed_by` in all three call sites
  (`Admin\ParcelController` x2, `Customer\ParcelController` x1). Verified
  fixed via direct tinker reproduction before and after.

### PHASE D (continued) — Receiver management
- `Customer\ReceiverController@index/create/store` — scoped to
  `receivers.booking_customer_id = <own customer_id>`; `store` inserts with
  `booking_customer_id` forced server-side.
- `resources/views/customer/receivers/index.blade.php`, `create.blade.php`.

### PHASE E — Customer self-booking (confirmed with user before building)
- `Customer\ParcelController@create/store` — customer books their own
  parcel. `sender_customer_id` is **always** `CustomerContext::id()`, never
  read from the form. The chosen `receiver_id` is verified to belong to the
  caller (`WHERE receiver_id = ? AND booking_customer_id = ?`) before the
  insert — a hidden/tampered `receiver_id` for another customer's receiver
  is rejected with 403.
- No `book_parcel` stored procedure exists yet (the original 14-day plan's
  "Day 10 procedure" never landed — this project's actual Day 10 became the
  RBAC foundation instead, and stored procedures are still unbuilt). Reused
  the same raw-SQL transaction shape as `Admin\ParcelController@store`
  (NEXTVAL → insert parcel → insert history row → compute + insert fee)
  rather than inventing a procedure ahead of the lab progression.
- Routes added: `GET/POST customer/parcels/create|store`,
  `GET/POST customer/receivers, /create`.

### PHASE F — Verified (via tinker, `Auth::loginUsingId()` + direct controller calls)
| Check | Result |
|---|---|
| Dashboard shows only `customer_id=1000`'s parcels/stats | ✅ 2 parcels, correct stats |
| `/track` and customer `show` render identical timeline for same code | ✅ via shared partial |
| View a parcel belonging to a different customer | ✅ `403 You do not have access to this parcel.` |
| View a non-existent tracking code | ✅ `404` |
| Add a receiver → appears in own list, not in another customer's list | ✅ |
| Book a parcel → parcel + fee + history rows created, `sender_customer_id` forced | ✅ |
| Book with a foreign `receiver_id` (simulated tampered form field) | ✅ `403 That receiver does not belong to your account.` |
| Admin `updateStatus` still works after the `:by` → `:changed_by` rename | ✅ history row inserted correctly |

### PHASE G — Final Role-Based Access Control Audit

**Route inventory by role** (`php artisan route:list`, 65 routes total):

| Role | Prefix | Middleware | Routes |
|------|--------|------------|--------|
| Public | — | none | `/`, `/track` (GET+POST), `/access-denied`, auth.php (login/register/password/verify) |
| Admin | `admin/` | `auth`, `role:admin` | dashboard; customers/branches/riders resource (7 each); parcels index/create/store/show + update-status; reports; operations |
| Branch Mgr | `branch/` | `auth`, `role:branch_mgr` | dashboard only (**stub**) |
| Rider | `rider/` | `auth`, `role:rider` | dashboard only (**stub**) |
| Customer | `customer/` | `auth`, `role:customer` | dashboard; parcels index/create/store/show; receivers index/create/store |
| Any authenticated | — | `auth` | `/dashboard` (smart redirect), `/profile` (edit/update/destroy) |

**Middleware check**: every non-public group above declares `role:<x>` in
`routes/web.php` — confirmed by reading the file directly (Section "Route
reorganization", Day 10). No role group is missing its middleware.

**Ownership-check audit** (single-record fetch/mutate methods — role
middleware proves *who*, this proves *what they may touch*):

| Controller@method | Scope needed? | Ownership check present? |
|---|---|---|
| `Admin\CustomerController` show/edit/update/destroy | No — admin manages all customers by design | N/A (global scope is the intended design) |
| `Admin\BranchController` show/edit/update/destroy | No — same | N/A |
| `Admin\RiderController` show/edit/update/destroy | No — same | N/A |
| `Admin\ParcelController` show/updateStatus | No — same | N/A |
| `Customer\ParcelController@show` | Yes — must be sender | ✅ `abort_if($parcel->sender_customer_id != $customerId, 403, ...)` |
| `Customer\ParcelController@store` | Yes — receiver must be caller's own | ✅ ownership query before insert; `sender_customer_id` forced server-side |
| `Customer\ReceiverController@store` | Yes — writes as caller | ✅ `booking_customer_id` forced server-side (no show/edit/destroy built — Phase D only asked for index/create/store) |
| `ProfileController` edit/update/destroy | Yes — own account only | ✅ implicit — operates on `$request->user()` only, no ID route param exists to spoof |
| `Branch\DashboardController@index` | N/A today | **Gap (deferred, not silent)**: no query, no scoping — placeholder only |
| `Rider\DashboardController@index` | N/A today | **Gap (deferred, not silent)**: no query, no scoping — placeholder only |

**Gaps found and their status**:
1. Branch manager and rider dashboards remain unbuilt placeholders. Neither
   `branches` nor `riders` has a `user_id` FK yet — needed before either role
   can be scoped to "my branch" / "my deliveries," the same way `customers`
   now has one. Follow-up carried forward from the Day 10 note.
2. No stored procedures exist yet (`sp_*`, `book_parcel`, etc.) — still Day
   12's original scope per the 14-day plan, not done in this pass. Booking
   (admin and customer) uses raw-SQL transactions, consistently.
3. `:by` bind-name bug (fixed today) shows the raw-SQL controllers had never
   been exercised end-to-end with a real request before — worth a quick
   smoke test after any future raw-SQL controller changes.

## Day 13 — Homepage Cleanup + Login-Redirect Verification (2026-07-01)

### Pre-flight check
Both requested changes turned out to be partially already done from Day 10/12
work — verified against current code before touching anything, rather than
following the prompt's instructions blind:
- **Role-based post-login redirect already existed.** `AuthenticatedSessionController::store()`
  already calls `redirect()->intended(RoleRedirect::path(auth()->user()->role))`,
  and `admin.dashboard` / `branch.dashboard` / `rider.dashboard` /
  `customer.dashboard` are all real, `role:*`-middleware-protected routes
  (`php artisan route:list --name=dashboard` confirmed all 4 + the smart
  `/dashboard` redirect). **Did not** add the prompt's suggested placeholder
  `Route::get('/admin/dashboard', fn () => view('dashboard'))`-style routes —
  doing so would have duplicated real routes and, since those placeholders
  sit outside any `role:` middleware, would have let *any* authenticated user
  reach `/admin/dashboard` etc., undoing the Day 12 RBAC audit.
- **Homepage cleanup was still needed** — `welcome.blade.php` genuinely still
  had the full Laravel starter template (embedded Tailwind fallback CSS,
  "Let's get started" doc/Laracasts links, "Deploy now" button, both light/dark
  Laravel logo SVGs) sitting below the Track-a-Parcel box from Day 6.

### PHASE A — `resources/views/welcome.blade.php` rewritten
- Removed entirely: the "Let's get started" section, "Deploy now" button,
  Laravel wordmark/hero SVGs (light + dark variants), Documentation/Laracasts
  links, and the ~40KB embedded Tailwind fallback stylesheet.
- Kept: top nav (Log in / Register, or Dashboard link if already authed),
  the existing Track-a-Parcel hero box (unchanged, still POSTs to `route('track')`).
- Added: three feature cards below the tracking box — Book Parcels,
  Real-time Tracking, Branch Network — using the same card style as the rest
  of the app (white bg, gray border, rounded-xl).

### PHASE B — Verified, no code change needed
Confirmed via direct code read (not assumption) that Phase B's requested
behavior already exists; no route or controller changes made.

### PHASE C — Tested
Ran `php artisan serve` on a scratch port and drove it with `curl` (cookie
jar + CSRF token extraction) rather than a manual browser pass, since this
session has no browser:
| Check | Result |
|---|---|
| Homepage 200, zero "Get started"/"Deploy now"/"Laracasts" matches | ✅ |
| `/login` reachable (200) | ✅ |
| `admin@test.com` login → redirect | ✅ `/admin/dashboard` |
| `branchmgr@test.com` login → redirect | ✅ `/branch/dashboard` |
| `rider@test.com` login → redirect | ✅ `/rider/dashboard` |
| `customer@test.com` login → redirect | ✅ `/customer/dashboard` |
| Anonymous POST `/track` with `CDB202600001` from homepage's box | ✅ returns tracking result, no login required |

## Day 14 — Public Tracking Removed (2026-07-01)

### Design decision
Public, unauthenticated parcel tracking was removed by request. All parcel
visibility is now behind login — there is no way to look up a tracking code
without an account.

### PHASE A — `resources/views/welcome.blade.php`
- Removed the Track-a-Parcel hero box (input + form) entirely.
- Replaced it with a static heading ("Courier Parcel Tracking & Delivery
  Management System"), a one-line description, and a "Log in to continue"
  button linking to `/login`. Kept the three feature cards (Book Parcels /
  Real-time Tracking / Branch Network) below it for context — still purely
  informational, nothing functional before login.

### PHASE B/C — Routes and controller removed
- Deleted `GET /track` (`track.form`) and `POST /track` (`track`) from
  `routes/web.php`.
- Deleted `app/Http/Controllers/TrackingController.php`.

### PHASE D — Views removed
- Deleted `resources/views/public/track.blade.php`; the now-empty
  `resources/views/public/` directory was removed too.
- `resources/views/partials/parcel-timeline.blade.php` (the shared timeline
  partial built on Day 12) was **kept** — it's still used by the
  authenticated `/customer/parcels/{tracking_code}` view, just no longer
  shared with a public page.

### PHASE E — Tracking moved inside the customer dashboard
- The customer dashboard's "Quick Track" box now POSTs to a new
  `customer.track` route (`POST customer/track`,
  `Customer\ParcelController@track`) instead of the deleted public route.
- `track()` doesn't re-implement the lookup — it validates `tracking_code`
  and redirects to the existing `customer.parcels.show` route, which already
  does `WHERE UPPER(tracking_code) = ?` plus the
  `sender_customer_id == caller's own customer_id` ownership check built on
  Day 12. One code path, so the "only your own parcels" rule can't drift
  between two implementations.
- Removed the sidebar "Track" link from `<x-customer-layout>` (tracking is
  now a widget on the dashboard, not a separate page) and removed the two
  dead "Track Parcel" links from the legacy `layouts/navigation.blade.php`
  (still used by the branch/rider dashboard stubs and a few Breeze-default
  pages) that pointed at the now-deleted `track.form` route.

### PHASE F — Tested
| Check | Result |
|---|---|
| Homepage: no tracking form/input anywhere | ✅ only the heading + CTA + cards |
| `GET /track` | ✅ `404` |
| `/login` reachable, "Log in to continue" button works | ✅ |
| Logged in as `customer@test.com`, POST own tracking code to `/customer/track` | ✅ redirects to `/customer/parcels/CDB202600001` |
| Same, with a tracking code belonging to a different customer | ✅ redirects then `403` (ownership check still enforced) |



## Project shape

Oracle-backed courier tracking/delivery app. Business schema (8 tables) is
raw SQL under `database/sql/`, run in order via `database/sql/run-all.sql`
(day-numbered comments therein: Day 2 users/roles/privileges, Day 3 tables,
Day 4 constraints + seed data, Day 9 PL/SQL audit procedures, Day 10 table
grants, Day 12 customer↔user linking). Laravel 12 app on top talks to Oracle
almost entirely via raw `DB::select/insert/update` — see
`docs/schema-design.md` for what's core schema vs. app-layer only.

Roles: `admin`, `branch_mgr`, `rider`, `customer` (`users.role`, Laravel-side).
Per-role dashboards live under `app/Http/Controllers/{Admin,Branch,Rider,Customer}`
with matching `x-*-layout` Blade components.

Prior to today: Admin dashboard + full CRUD (customers/branches/riders/parcels),
Customer dashboard + parcel booking/tracking, join/aggregate/subquery/PL/SQL
lab controllers, Rider and Branch dashboard route stubs.

## 2026-07-03 — Branch Manager role: dashboard + scoped parcel view

**Verified first:** `/admin/dashboard` renders correctly (confirmed via a
live login as `admin@test.com` — 31 parcels, 9 in transit, 11 active riders,
matching direct DB queries).

**Branch scoping (`users.branch_id`):** no existing association between a
`branch_mgr` user and a branch, so added it:
- Migration `2026_07_03_030828_add_branch_id_to_users_table.php` — nullable
  `branch_id` on `users`, no DB-level FK (cross-managed-schema; see
  `docs/schema-design.md` → Application-layer associations).
- `branchmgr@test.com` (user id 43) set to `branch_id = 1000` (Dhaka Central
  Branch) via tinker.

**Built:**
- `resources/views/components/branch-layout.blade.php` — sidebar layout
  (Dashboard, Parcels), following the existing `x-admin-layout` /
  `x-customer-layout` component convention rather than a `layouts/*.blade.php`
  file, to stay consistent with the rest of the app. Header shows the
  manager's branch name.
- `Branch\DashboardController` — stats scoped to
  `origin_branch_id = ? OR destination_branch_id = ?`: total parcels at
  branch, today's bookings, OUT_FOR_DELIVERY count, status breakdown, recent
  parcels.
- `Branch\ParcelController` — `index` (branch-scoped list with status +
  search filters), `show` (branch-scoped, `abort(403)` if the parcel exists
  but doesn't touch this branch), `updateStatus` (same ownership check before
  writing, then plain `DB::update` + `DB::insert` into
  `parcel_status_history` — mirrors `Admin\ParcelController::updateStatus`
  exactly, since no `update_parcel_status` procedure or status-change
  trigger exists yet; the trigger is explicitly deferred to a later day per
  the comment in `database/sql/02-schema/07-parcel-status-history.sql`).
- Routes added under the existing `branch_mgr` group in `routes/web.php`:
  `branch.parcels.index`, `branch.parcels.show`, `branch.parcels.updateStatus`.

**Verified end-to-end** (live login as `branchmgr@test.com`, branch 1000):
- Dashboard stats (14 total / 2 OUT_FOR_DELIVERY) match direct DB queries.
- Parcels index returns exactly the 14 tracking codes touching branch 1000.
- `GET /branch/parcels/1003` (origin=1003, dest=1005 — doesn't touch 1000)
  → **403** "This parcel does not belong to your branch."
- `POST /branch/parcels/1003/update-status` → **403** (same check applied to
  the write path, not just the read path).
- `POST /branch/parcels/1000/update-status` (owned parcel) → 302 success,
  `parcels.current_status` updated to `IN_TRANSIT`, and a new
  `parcel_status_history` row written with `changed_by = 'Test Branch Manager'`.

**Housekeeping note:** the working tree had pre-existing uncommitted changes
unrelated to this work (stripped header comments across several SQL setup
files) from a prior session — left untouched per instruction. The Day 9
PL/SQL procedure files and `run-all.sql`, which had been deleted on disk,
were restored before starting.

## 2026-07-03 — Rider role: dashboard + delivery attempt logging

**Verified first:** branch manager dashboard/scoping from the previous entry
still holds (branch 1000 total-parcels count re-checked: 14, matches DB).

**Rider scoping (`riders.user_id`):** riders had no link to `users` at all
(confirmed: `RIDER_ID FULL_NAME PHONE VEHICLE_TYPE ASSIGNED_BRANCH_ID ACTIVE_FLAG`,
no `user_id`). Added as raw SQL rather than a migration, since `riders` is a
raw-SQL-managed table:
- `database/sql/02-schema/12-alter-riders-user-link.sql` — `ALTER TABLE riders
  ADD user_id NUMBER`, no FK (logical link only, matches the
  `users.branch_id` precedent). Run via `sqlplus cdb_admin/...@XE` directly.
  Added to `run-all.sql` as "Day 13: rider account linking" (the `12-` file
  prefix just fills the numbering gap before `13-alter-customers-user-link.sql`;
  it doesn't correspond to Day 12).
- `rider@test.com` (`users.id = 44`) linked to `riders.rider_id = 1002`
  (Mahfuzur Rahman — chosen because it already had both an `IN_TRANSIT` and
  an `OUT_FOR_DELIVERY` parcel in seed data, better for testing than a rider
  with only one active job).
- Gotcha: `UPDATE riders SET user_id = :uid ...` fails with
  `ORA-01745: invalid host/bind variable name` — `UID` is an Oracle reserved
  pseudo-column. Bind variable renamed to `:userid`.
- Documented in `docs/schema-design.md`, including a correction to the prior
  entry's reasoning (see below).

**Correction to 2026-07-03 branch manager entry:** it claimed no FK was added
to `users.branch_id` because of "cross-managed-schema" limitations. That's
inaccurate — `users` and the business tables are all in the same `cdb_admin`
Oracle schema, and `customers.user_id` (Day 12) already has a real FK to
`users.id`. The actual reason is a deliberate choice to skip DB-level FKs for
columns that only exist for role-scoping. `schema-design.md` now states this
correctly and contrasts it with `customers.user_id`.

**Built:**
- `resources/views/components/rider-layout.blade.php` — mobile-first layout
  (top bar + bottom tab nav, large tap targets, single column, `max-w-xl`),
  following the `x-*-layout` component convention rather than
  `layouts/rider.blade.php`, same as the branch manager layout decision.
- `Rider\DashboardController` — resolves `rider_id` from
  `riders.user_id = auth()->user()->id`; active jobs = parcels with
  `assigned_rider_id = ?` and status `IN_TRANSIT`/`OUT_FOR_DELIVERY`; today's
  delivered/failed counts from `delivery_attempts` (`TRUNC(attempted_at) =
  TRUNC(SYSDATE)`). Handles the unlinked-rider case gracefully (empty state
  instead of erroring).
- `resources/views/rider/dashboard.blade.php` — card-per-parcel (not a
  table): tracking code, receiver name/phone/address, weight, status badge,
  a big "Log Delivery Attempt" button per card.
- `Rider\DeliveryController` — `logForm`/`logAttempt`, both go through
  `resolveOwnedParcel()` first: resolves the rider's own `rider_id`, loads
  the parcel, `abort(403)` if `assigned_rider_id` doesn't match — same
  ownership-check shape as `Branch\ParcelController`. On submit: inserts into
  `delivery_attempts`; on success, transitions the parcel to `DELIVERED`
  (`delivered_at = SYSDATE`) + logs `parcel_status_history`; on failure,
  counts prior failed attempts and auto-transitions to `RETURNED` (+ history
  row) once the count reaches 3.
- No `update_parcel_status` procedure call and no `trg_auto_return` DB
  trigger — neither exists yet (same situation as the branch manager work;
  see `schema-design.md` → "No DB triggers/procedures for status transitions").
  The 3-strikes auto-return rule is implemented as an app-layer check in
  `DeliveryController`, kept consistent with how every other controller
  mutates `current_status`.
- `resources/views/rider/delivery/log.blade.php` — radio Success/Failed
  (Alpine `x-data`/`x-model`), failure reason textarea shown only when
  Failed is selected (`x-show`/`x-cloak`).
- Routes added under the existing `rider` group: `rider.delivery.log` (GET
  form), `rider.delivery.store` (POST, handled by `logAttempt`).

**Verified end-to-end** (live login as `rider@test.com`, rider 1002):
- Dashboard active jobs = exactly parcels 1006 (`IN_TRANSIT`) and 1014
  (`OUT_FOR_DELIVERY`) — the rider's third parcel (1018, already `DELIVERED`)
  correctly excluded. Today's stats start at 0/0.
- `GET /rider/delivery/1005/log` (parcel assigned to rider 1000, not 1002)
  → **403** "This parcel is not assigned to you."
- 3× `POST /rider/delivery/1006/log` with `outcome=failed` → after the 3rd,
  `parcels.current_status` = `RETURNED`, `parcel_status_history` gained a row
  (`remarks = 'Auto-returned after 3 failed delivery attempts'`), all 3 rows
  present in `delivery_attempts`.
- `POST /rider/delivery/1014/log` with `outcome=success` →
  `current_status` = `DELIVERED`, `delivered_at` set, new
  `parcel_status_history` row (`remarks = 'Delivered by rider'`).
- Dashboard re-checked after both attempts: active jobs = 0 ("No active
  deliveries 🎉"), today's stats = 1 delivered / 3 failed.

## 2026-07-03 — PL/SQL layer: functions, procedures, controller wiring

Built the first real Oracle PL/SQL objects in this project (5 functions, 3
procedures) and wired 3 of them into the existing manual-raw-SQL controllers
identified in the prompt: `Admin\ParcelController@store/@updateStatus`,
`Customer\ParcelController@store`, `Branch\ParcelController@updateStatus`,
`Rider\DeliveryController@logAttempt`, `Customer\DashboardController`.

**All 8 objects compiled clean and are STATUS=VALID** (`SELECT object_name,
object_type, status FROM user_objects WHERE object_type IN
('FUNCTION','PROCEDURE')`):

| Type | Name | Status |
|---|---|---|
| FUNCTION | `CALCULATE_FEE` | VALID |
| FUNCTION | `GET_PARCEL_STATUS` | VALID |
| FUNCTION | `RIDER_SUCCESS_RATE` | VALID |
| FUNCTION | `CUSTOMER_TOTAL_SPEND` | VALID |
| FUNCTION | `BRANCH_REVENUE` | VALID |
| PROCEDURE | `BOOK_PARCEL` | VALID |
| PROCEDURE | `ASSIGN_RIDER` | VALID |
| PROCEDURE | `UPDATE_PARCEL_STATUS` | VALID |

Files: `database/sql/07-functions/01..05-*.sql`, `database/sql/06-procedures/01..03-*.sql`,
all added to `run-all.sql` under "Procedures & Functions" (functions before
procedures, since `book_parcel`'s trigger dependency in Prompt B needs
`calculate_fee` to already exist — see the note in `01-book-parcel.sql`).
`calculate_fee`'s `RETURN` type in the function header is unconstrained
`NUMBER` (Oracle disallows a constrained `RETURN NUMBER(8,2)` on a stored
function signature) — the `NUMBER(8,2)` precision from the prompt is applied
internally via a local `v_total NUMBER(8,2)` variable instead.

**Function sanity checks** (via `SELECT fn(...) FROM DUAL`):
`calculate_fee(3)=110`, `calculate_fee(10)=330`, `calculate_fee(20)=750`
(tier boundaries correct); `get_parcel_status('CDB202600001')='IN_TRANSIT'`,
`get_parcel_status('BOGUS')='NOT_FOUND'`; `rider_success_rate(1002)=40`,
`rider_success_rate(<rider with 0 attempts>)=0` (no divide-by-zero error).

**Controller wiring — book_parcel** (`Admin\ParcelController@store`,
`Customer\ParcelController@store`): raw PDO `BEGIN book_parcel(...); END;`
with an `IN_OUT` bind for `:tc`, exactly the pattern given in the prompt.
Wrapped in try/catch; on `PDOException` the Oracle message is extracted with
`preg_match('/ORA-\d+:\s*([^\n]+)/', ...)` (the driver's exception message is
multi-line — `Error Code`, `Error Message: ORA-N: <text>`, then
`ORA-06512: at ...` stack lines — so this grabs only the first `ORA-N:` line,
which is the one our own `RAISE_APPLICATION_ERROR` raised, not the trace).
Admin's manual `INSERT INTO fees` and `INSERT INTO parcel_status_history`
blocks were removed as instructed — the procedure inserts the BOOKED history
row itself, and the fee row is **not** created until Prompt B's
`trg_auto_fee` exists (see gap below).

Two side effects of this wiring, both within the "book_parcel doesn't take a
rider parameter" constraint from the prompt's exact signature:
- The admin create form's optional `assigned_rider_id` dropdown is no longer
  connected to anything — `book_parcel` has no rider parameter, and Phase D
  didn't ask for `assign_rider` to be wired anywhere today, so it was left
  unused (built, compiles, not called). Not a silent gap: noted here.
- **Bug fixed in passing**: `Customer\ParcelController@store` used to
  `redirect()->route('customer.parcels.show', $id)` with the numeric
  `parcel_id`, but that route is keyed on `{tracking_code}` — this was
  silently 404ing after every successful customer booking before today
  (never caught because nothing in this project's test coverage clicked
  through a real booking as `customer@test.com` until now). Since
  `book_parcel`'s OUT param already hands back the tracking code, the
  redirect now uses `$trackingCode` directly — fixes the bug and needs one
  less query than the old id-lookup approach would have.
- Minor fidelity loss: `book_parcel`'s history remark is hardcoded to
  `'Parcel booked'` for every caller (per the prompt's exact INSERT). The
  customer controller used to write a distinct `'Booked online by customer'`
  remark; that distinction is gone now that both paths share one procedure.

**Controller wiring — update_parcel_status** (`Admin\ParcelController@updateStatus`,
`Branch\ParcelController@updateStatus`, `Rider\DeliveryController@transitionStatus`):
same raw-PDO pattern, no OUT param needed. Branch's existing branch-ownership
check and Rider's existing rider-ownership check both still run *before* the
procedure call, unchanged — the procedure adds status-transition validity on
top of, not instead of, those checks.

`Rider\DeliveryController` keeps the delivery_attempts `INSERT` as plain
`DB::insert` (per instructions — no procedure for that), but
`transitionStatus()` (used for both the success→DELIVERED path and the
3-strikes→RETURNED path) now calls `update_parcel_status` instead of doing
its own `UPDATE`+history-insert. **New behavior worth flagging**: this makes
the rider path subject to the same state-machine enforcement as admin/branch
for the first time — e.g. marking an `IN_TRANSIT` parcel "Delivered" directly
(skipping `OUT_FOR_DELIVERY`) now gets rejected with ORA-20007 instead of
silently succeeding. Verified this doesn't crash: wrapped the
`DB::transaction()` call in try/catch, `PDOException` → `back()->withErrors([...])`,
which renders through `rider/delivery/log.blade.php`'s existing
`@if($errors->any())` block — no view changes needed there, since that
generic error-list block already existed.

**Error-message visibility — one exception to "no view changes"**: the
prompt says to flash Oracle errors via `with('error', ...)` for Admin/Branch,
but none of `admin-layout.blade.php` / `branch-layout.blade.php` /
`customer-layout.blade.php` rendered `session('error')` anywhere (only
`session('success')` existed, and Rider's view uses the unrelated `$errors`
bag). Following the instruction literally would have made every flashed
error invisible in the browser, failing Phase F's explicit "expect an error
message" check. Added a 3-line `@if(session('error'))` red banner to each of
those three layout components, mirroring the `session('success')` block
already present in each — a one-line-per-layout addition, not a new view or
route, and necessary for the explicitly-requested behavior to actually be
observable.

**Phase E — Customer\DashboardController**: `Total Spend` stat now calls
`SELECT customer_total_spend(:id) AS spend FROM DUAL` instead of the inline
`SUM(fees.total_amount)` join. Verified: renders `৳0.00` for
`customer@test.com` (customer_id 1000), matching direct function output.

**Verified end-to-end** (live server, real logins, not just code review):
| Check | Result |
|---|---|
| Admin books a parcel via the form | ✅ tracking code `CDB202601304` generated, redirects to `/admin/parcels/1304` |
| New parcel has exactly 1 `BOOKED` history row | ✅ |
| New parcel has a `fees` row | ❌ **expected** — 0 rows, pending Prompt B's `trg_auto_fee` |
| Customer (`customer@test.com`) books a parcel | ✅ redirects to `/customer/parcels/CDB202601305` (tracking-code bug fix confirmed) |
| Customer dashboard Total Spend renders via `customer_total_spend()` | ✅ `৳0.00`, matches direct function call |
| Admin: `BOOKED` → `DELIVERED` directly (invalid transition) | ✅ red banner "Invalid status: DELIVERED", status unchanged, no crash |
| Admin: `BOOKED` → `IN_TRANSIT` (valid transition) | ✅ succeeds, no error banner |
| Status update via `update_parcel_status` does **not** add a history row | ❌ **expected** — pending Prompt B's `trg_status_history` (procedure only updates `parcels`, per its own header comment) |
| Branch manager updates status of a parcel touching their branch | ✅ `OUT_FOR_DELIVERY` applied via the same procedure, existing branch-ownership check still enforced first |
| `update_parcel_status` on a terminal-state parcel (`RETURNED`) | ✅ `ORA-20006` raised and caught cleanly (tested directly against the procedure — same path `Rider\DeliveryController` now uses) |

### Known gaps, carried forward to Prompt B
1. **No fee auto-creation.** `book_parcel` no longer inserts a `fees` row
   (that logic was deleted from the PHP controllers per instructions).
   Booking a parcel today leaves it with zero fee rows until `trg_auto_fee`
   (fired on `INSERT INTO parcels`, calling `calculate_fee`) exists.
2. **No automatic status-history logging on UPDATE.** `update_parcel_status`
   only updates `parcels.current_status`; every status change made through
   it today (admin, branch, rider) leaves `parcel_status_history` exactly as
   it was before the call. `trg_status_history` (fires on UPDATE of
   `current_status`) is what's supposed to close this gap. `book_parcel`'s
   *initial* BOOKED row is unaffected — that one is still inserted manually
   by the procedure itself, since a trigger firing only on UPDATE can never
   catch an INSERT's starting state.
3. **`assign_rider` and 3 of the 5 functions are compiled but unused.**
   `assign_rider`, `get_parcel_status`, `rider_success_rate`, and
   `branch_revenue` aren't called from any controller yet — the prompt
   scoped today's wiring to `book_parcel`, `update_parcel_status`, and
   `customer_total_spend` only, with the rest earmarked for "Prompt C"
   reports and future tracking-check wiring.
4. **Rider's 3-strikes auto-return app-layer check is still in place**,
   per instructions — Prompt B replaces it with `trg_auto_return`.

## 2026-07-03 — Prompt B: 4 Oracle triggers, automated fee/history/return chain

Built the 4 triggers that close the gaps documented above. All ENABLED
(`SELECT trigger_name, status FROM user_triggers`):

| Trigger | Table | Event | Status |
|---|---|---|---|
| `TRG_STATUS_HISTORY` | `PARCELS` | `UPDATE OF current_status` | ENABLED |
| `TRG_AUTO_FEE` | `PARCELS` | `INSERT` | ENABLED |
| `TRG_AUTO_RETURN` | `DELIVERY_ATTEMPTS` | `INSERT` (compound) | ENABLED |
| `TRG_RIDER_ACTIVE` | `PARCELS` | `INSERT OR UPDATE OF assigned_rider_id` | ENABLED |

Files: `database/sql/08-triggers/01..04-*.sql`, appended to `run-all.sql`
after Procedures & Functions (they depend on `calculate_fee`).

- **`trg_status_history`** — auto-logs every `current_status` UPDATE (via
  `update_parcel_status` or `trg_auto_return`'s own UPDATE) into
  `parcel_status_history`. Never fires on INSERT, so `book_parcel`'s manual
  BOOKED-row insert stays — confirmed unchanged, still the only way that
  first row gets written.
- **`trg_auto_fee`** — fires on every `parcels` INSERT, computes the fee via
  `calculate_fee(:NEW.weight_kg)`. `database/sql/03-seed/08-fees.sql`
  (previously 30 manual `INSERT INTO fees` statements) is now cleared to a
  comment — re-running it after this trigger exists would violate
  `fees.parcel_id`'s UNIQUE constraint. The existing 30 seed fee rows (with
  their specific `paid_flag`/`paid_at` values) are untouched; the trigger
  only affects new inserts going forward.
- **`trg_auto_return`** — sets a parcel to `RETURNED` on its 3rd failed
  delivery attempt, which then fires `trg_status_history` automatically.
- **`trg_rider_active`** — `BEFORE INSERT OR UPDATE OF assigned_rider_id`,
  rejects assigning a rider whose `active_flag != 'Y'` (`ORA-20010`) or one
  that doesn't exist (`ORA-20011`) — a protection with no app-layer
  equivalent before today.

### Bug found and fixed: ORA-04091 mutating-table error in trg_auto_return

The literal trigger body from the prompt — a plain `AFTER INSERT ... FOR EACH
ROW` trigger that runs `SELECT COUNT(*) FROM delivery_attempts WHERE
parcel_id = :NEW.parcel_id AND success_flag = 'N'` inside its own body —
compiles clean but **fails at runtime on every single failed delivery
attempt** with `ORA-04091: table CDB_ADMIN.DELIVERY_ATTEMPTS is mutating,
trigger/function may not see it`. The prompt's own comment ("this trigger is
on delivery_attempts, not parcels, so there is no mutating-table issue") is
incorrect — the mutating-table restriction is about a row-level trigger
querying the *same table* whose triggering DML statement is still in
progress, which is exactly what this is: a trigger on `delivery_attempts`
querying `delivery_attempts`. Caught this via live testing (curl → the
rider's log form re-rendered with a red banner reading exactly that ORA-04091
message instead of redirecting to the dashboard) rather than by inspection —
worth remembering that trigger logic like this needs a real INSERT to prove
out, not just a clean compile.

**Fix**: rewrote `trg_auto_return` as a **compound trigger** (supported since
Oracle 11g, confirmed available on this XE 11.2.0.2 instance): the
`AFTER EACH ROW` section just collects the `parcel_id`s of failed attempts
from the current statement into a PL/SQL table; the `AFTER STATEMENT` section
(which runs after the triggering INSERT has fully completed, so the table is
no longer mutating) does the `COUNT(*)` and the conditional `UPDATE`. Same
externally-observable behavior, no `RAISE_APPLICATION_ERROR`/signature
change — this is purely an internal implementation fix. Full before/after is
in `database/sql/08-triggers/03-trg-auto-return.sql`'s header comment.

### Behavior change worth flagging: `changed_by` on trigger-logged rows

`trg_status_history` uses Oracle's `USER` (the DB session user) for
`changed_by`, per the prompt's exact spec. Since every Laravel request
connects as the single `cdb_admin` DB user regardless of which app user is
logged in, **every trigger-logged history row now shows `changed_by =
'CDB_ADMIN'`**, not the actual admin/branch-manager/rider name. This is a
real reduction in audit-trail usefulness compared to the old manual inserts
(which used `auth()->user()->name`) — confirmed via a live status update as
`admin@test.com`: the row read `CDB_ADMIN`, not `Test Admin`. `book_parcel`'s
manual BOOKED-row insert is unaffected (it still writes the real app
username) since that one was never replaced by a trigger. Implemented as
specified rather than deviating, since the prompt's trigger body explicitly
uses `USER`, but flagging this clearly since it's a meaningful behavior
change nobody asked to sign off on.

### Phase C — Rider\DeliveryController@logAttempt

Removed the app-layer 3-strikes block entirely (the `$failedCount >= 3` check
and its manual `transitionStatus(..., 'RETURNED', ...)` call). The unused
`MAX_FAILED_ATTEMPTS` constant was removed too — its only reference was in
the deleted block. `logAttempt` now only inserts the `delivery_attempts` row
and, for a successful outcome, calls `update_parcel_status` to move the
parcel to `DELIVERED` — everything else (auto-return, history logging) is
now the database's responsibility, with a comment saying so at the top of
the try block.

### Phase D — booking controllers

Confirmed neither `Admin\ParcelController@store` nor
`Customer\ParcelController@store` has any `INSERT INTO fees` left (both were
already clean from Prompt A). Added a one-line `// Fee created automatically
by trg_auto_fee` comment above each `book_parcel` call for clarity.

### Verified end-to-end (live server + direct DB, real logins)

| Check | Result |
|---|---|
| Admin books a parcel (weight 6.5kg) | ✅ tracking code generated, redirects normally |
| `fees` row auto-created by `trg_auto_fee`, not the controller | ✅ exactly 1 row, `total_amount = calculate_fee(6.5) = 242.5` (tiered), matches direct function call |
| `parcel_status_history` after booking | ✅ exactly 1 row: `BOOKED`, inserted manually by `book_parcel`, unaffected by the new triggers |
| Admin updates status `BOOKED → IN_TRANSIT` | ✅ succeeds; `parcel_status_history` now has 2 rows — the manual `BOOKED` one plus an auto-logged `IN_TRANSIT` one from `trg_status_history` |
| Rider logs 3 failed attempts on an assigned parcel | ❌ **first attempt**: `ORA-04091` mutating-table error surfaced as a red banner (bug described above) |
| Same, after the compound-trigger fix | ✅ all 3 succeed; parcel status auto-flips to `RETURNED`; `parcel_status_history` gains a `RETURNED` row auto-logged by `trg_status_history` (`changed_by = CDB_ADMIN`, per the note above) |
| `assign_rider` procedure still works with `trg_rider_active` in place (active rider) | ✅ `BOOKED → IN_TRANSIT`, rider assigned, `trg_status_history` logged it |
| Assigning an inactive rider (`active_flag = 'N'`, rider 1007) | ✅ `ORA-20010` raised and caught, parcel unchanged |
| Assigning a nonexistent rider (`rider_id = 9999`) | ✅ `ORA-20011` raised and caught |

**Note on the `trg_rider_active` test**: there is currently no controller
path that sets `assigned_rider_id` at all — `book_parcel` doesn't accept a
rider parameter (documented in the previous entry) and `assign_rider` isn't
called from any route yet. Tested the trigger directly (via a raw `UPDATE`
and via `assign_rider`) rather than "from the admin parcel edit form" as
originally asked, since no such form exists in this codebase — there's only
`admin/parcels/create`, `index`, `show`, and `updateStatus`, no `edit`. The
trigger itself is fully verified; wiring a rider-assignment UI is unbuilt,
same gap as before.

### Known gaps remaining
1. `get_parcel_status`, `rider_success_rate`, `branch_revenue`, and
   `assign_rider` are still compiled but unused by any controller/route.
2. No UI exists for assigning/reassigning a rider to a parcel post-booking.
3. `changed_by` on every trigger-logged history row reads `CDB_ADMIN`
   instead of the acting app user (see note above) — a real but
   spec-directed regression, not something to silently patch without
   sign-off.

## 2026-07-03 — Admin Analytics section (replaces /lab/aggregates, /lab/subqueries)

Built a real "Analytics" section for ops-manager-facing reports: 4 routes, 1
controller (`Admin\AnalyticsController`, 4 methods, all raw `DB::select`, no
Eloquent), 4 views, and a nav dropdown. Finally puts `rider_success_rate` and
`branch_revenue` to use — the last 2 of the 5 functions from the PL/SQL-layer
entry that were still unused.

**Routes** (`admin.analytics`, `admin.analytics.branches`,
`admin.analytics.riders`, `admin.analytics.parcels`) — added under the
existing `role:admin` group in `routes/web.php` using the group's `/admin`
prefix convention (`/analytics`, not `/admin/analytics`, since the group
already adds `admin/` — copying the prompt's fully-qualified paths literally
would have doubled the prefix to `/admin/admin/analytics`).

### Bugs found in the prompt's SQL, both caught by actually running the queries against this Oracle 11g XE instance before writing any PHP

1. **`riders()` query — `ORA-00923: FROM keyword not found where expected`.**
   The column alias `successful` (`SUM(CASE WHEN da.success_flag='Y' ...) AS successful`)
   fails because `SUCCESSFUL` is an Oracle reserved word and can't be used
   unquoted as an alias. Renamed to `successful_cnt` everywhere (controller,
   view). Isolated this by testing `SELECT 1 AS successful FROM DUAL` alone —
   confirmed the alias itself was the problem, not the surrounding JOINs/aggregates.
2. **`parcels()` weight-distribution query — `ORA-00979: not a GROUP BY expression`.**
   The `SELECT` list's `CASE` expression labels the top band `'16-50 kg'` but
   the `GROUP BY`'s `CASE` expression (copy-pasted with a drifted label)
   labels the same band `'Over 15 kg'`. Oracle requires the `GROUP BY`
   expression to textually match a `SELECT`-list expression when grouping by
   an expression rather than a column — since the two `CASE` blocks differ,
   Oracle doesn't recognize them as the same grouping key. Fixed by making
   both `CASE` blocks identical (`'16-50 kg'` in both).

Every other query from the prompt (overview stats, branch performance,
underperforming branches, rider leaderboard, riders needing attention,
volume funnel, stuck parcels) ran correctly as given, verified individually
via `DB::select` in tinker before wiring into the controller.

### `index()` — Overview
4 stat cards: overall success rate (`31.4%`), average delivery days
(`ROUND(AVG(delivered_at - booked_at), 1)` — added the `ROUND` myself since
the prompt's raw `AVG` returns a ~40-digit decimal in Oracle and the prompt
didn't specify rounding for this one, only for the success-rate stat),
busiest branch (`Dhaka Central Branch` — plausible, most of this project's
own live-testing bookings landed there), and top rider by success rate among
riders with ≥5 attempts (`Mahfuzur Rahman`, `25%` — the only rider that
clears the 5-attempt bar right now, and their low rate is a direct artifact
of the 3-strikes-auto-return testing done in the trigger-work session; not a
seed-data anomaly, just accumulated real test data).

### `branches()` — Branch Performance
All 6 branches shown (all have `total_parcels > 0`, so the `HAVING` clause
doesn't currently filter anything out). 5 of 6 branches fall under the
"underperforming" `HAVING COUNT(...) < AVG(...)` threshold — again because
Dhaka Central's parcel count is skewed high from repeated live testing, which
pulls every other branch below the average. Underperformer rows get an amber
highlight + badge in the table.

### `riders()` — Rider Performance
Leaderboard sorted `success_rate DESC NULLS LAST` (riders with zero attempts
sort to the bottom, shown as "No attempts" instead of a numeric badge).
Success-rate badges are color-coded by a PHP closure in the view
(`>=80` green, `>=60` yellow, else red / gray for null) rather than a
hardcoded class per row, per the "not hardcoded class" instruction. "Needs
Attention" section (riders with ≥3 attempts and <60% success) currently
shows the same single rider as the overview's top-rider card — same
underlying cause (only one rider has enough attempts to qualify for either
list right now).

### `parcels()` — Parcel Intelligence
Volume funnel ordered by the given `CASE` status-priority expression. Stuck
parcels (`IN_TRANSIT`/`OUT_FOR_DELIVERY` for >3 days): **11 parcels**, all
from original seed data (booked weeks ago, several never progressed past
`IN_TRANSIT`) — a red alert banner shows above the table only when the count
is nonzero. Weight distribution across the 3 tiered bands, average fee per
band shown to 2 decimals.

### Phase C — Admin nav
Replaced the entire dead "Lab Demos" dropdown (HTML-commented-out already,
pointing at `JoinController`/`AggregateController`/`SubqueryController`/`PlsqlController`
— none of which exist anywhere in this codebase, confirmed via
`find app/Http/Controllers -iname "*Join*" ...` returning nothing) with a
live "Analytics" dropdown in the same position, using the same Alpine
`x-data`/`x-show` pattern. Sub-items: Overview, Branch Performance, Rider
Performance, Parcel Intelligence — active-state highlighting via
`request()->routeIs('admin.analytics*')` for the dropdown button and
`request()->routeIs($routeName)` per sub-item.

### Phase D — Removing /lab/aggregates and /lab/subqueries
Confirmed there was nothing to remove: `app/Http/Controllers/Lab/` and
`resources/views/lab/` don't exist on disk, and `routes/web.php` has no
`/lab/*` routes — matches the Day 11 progress note ("Lab controllers ... were
not present in the current branch ... will 404 until restored"), which
apparently never happened. The only trace of the old lab pages was the dead
commented-out nav block removed in Phase C above.

### Verified end-to-end (live server, real logins)
| Check | Result |
|---|---|
| `/admin/analytics` (overview) | ✅ 200, 4 cards match direct DB query values exactly |
| `/admin/analytics/branches` | ✅ 200, all 6 branches listed, 5 underperformers amber-highlighted |
| `/admin/analytics/riders` | ✅ 200, leaderboard + Needs Attention section both populated |
| `/admin/analytics/parcels` | ✅ 200, red "stuck parcels" banner shown, funnel + weight tables render |
| `/lab/aggregates`, `/lab/subqueries` | ✅ both `404` |
| `customer@test.com` → `/admin/analytics` | ✅ `403` "You do not have access to this section." |
| `/admin/dashboard` after the layout edit | ✅ still 200, unaffected — regression check on the shared `admin-layout.blade.php` change |

### Known gaps
1. Overview's "top rider" and Rider Performance's "needs attention" sections
   are thin (1 qualifying rider each) purely because this seed dataset has
   had very few real delivery attempts logged outside of this project's own
   testing sessions — not a bug, just a small-data artifact worth knowing
   about before treating these numbers as meaningful in a demo.
2. `get_parcel_status` and `assign_rider` remain the only 2 of the original 5
   functions/procedures still unused by any controller.

## 2026-07-03 — Real filtering built into existing listings (replaces /lab/joins)

Every join type from the old `/lab/joins` demo (inner, outer/left, self,
cross via the pagination trick below, natural via shared keys) now does real
work in an actual feature — filtering/sorting/pagination on 4 existing
listing pages — instead of sitting in a labeled demo page.

### Bug found and fixed: `ORA-01745` on `:start` — another Oracle reserved word

Same class of bug as `:uid` from the rider-linking session: the prompt's
`:start`/`:end` bind-variable names for the ROWNUM pagination pattern fail
with `ORA-01745 (invalid host/bind variable name)` because `START` is an
Oracle reserved keyword (used in `CONNECT BY START WITH`) — confirmed by
testing `SELECT :start AS v FROM DUAL` in isolation before writing any PHP.
`:end` alone is fine; it's specifically `:start`. Renamed both to
`:row_start`/`:row_end` for symmetry and clarity. Documented inline in
`Admin\ParcelController::index()` so nobody reintroduces `:start` later.

### Phase A — `Admin\ParcelController@index`
Full rewrite: 9 optional filters (`tracking`, `status`, `origin_branch`,
`dest_branch`, `rider_id`, `weight_min`, `weight_max`, `date_from`,
`date_to`) built as a dynamic `$conditions`/`$bindings` array, a 5-column
sort whitelist (`SORT_COLUMNS` map — never interpolates a raw column name
from the request), and Oracle 11g ROWNUM pagination (10/page) exactly as
specified once the bind-name bug above was fixed.

One simplification from the prompt: the `COUNT(*)` query for pagination
skips all 6 joins and just runs `SELECT COUNT(*) FROM parcels p {$where}` —
every filterable column lives on `parcels` itself, and every join in the
base query is either an inner join on a `NOT NULL` FK (sender/receiver/origin/dest
are all required at insert time, so it can never drop a row) or a left join
(which also can't drop rows), so the count is provably identical either way
and cheaper to compute. Documented inline.

View (`resources/views/admin/parcels/index.blade.php`): collapsible filter
panel (Alpine, starts open if any filter is already active, closed
otherwise), all 9 filter inputs + sort/direction selects, result count,
removable filter tags (each links to the current query string with just that
one key stripped), numbered pagination links.

### Phase B — `Admin\CustomerController@index`
Added `search` (name/phone/email, `UPPER(...) LIKE UPPER(:x)`) and
`active_only` — a correlated `EXISTS` subquery (`EXISTS (SELECT 1 FROM
parcels WHERE sender_customer_id = c.customer_id AND current_status NOT IN
('DELIVERED','RETURNED'))`) — as a real listing filter, not a demo query.

### Phase C — `Branch\ParcelController@index`
Added `date_from`/`date_to` (`TRUNC(booked_at) BETWEEN ...`) and
`sort`/`dir` (2-column whitelist: `booked_at`, `current_status`) on top of
the existing `status`/`search` filters. The branch-ownership condition
(`origin_branch_id = ? OR destination_branch_id = ?`) stays the first thing
appended to `WHERE` and every other filter is `AND`-ed onto it — there's no
code path where a filter can replace or bypass that base condition.

### Phase D — `Rider\DashboardController@index` + `rider/dashboard.blade.php`
Added an Active/Completed toggle (`?view=active|completed`, defaults to
`active`). Completed pulls `DELIVERED`/`RETURNED` parcels for the rider,
ordered by an `outcome_at` column: `COALESCE(p.delivered_at, (SELECT
MAX(changed_at) FROM parcel_status_history WHERE parcel_id = p.parcel_id AND
status = p.current_status))` — `delivered_at` is only ever set for
`DELIVERED` parcels, so `RETURNED` ones fall back to the last matching
`trg_status_history` row for their current status. Verified this against
Oracle before wiring it in (same caution as everything else this session).

### Phase E — `/lab/joins`
Nothing to remove: `app/Http/Controllers/Lab/JoinController.php` and
`resources/views/lab/joins.blade.php` don't exist on disk, and
`routes/web.php` has no `/lab/joins` route — same situation as
`/lab/aggregates` and `/lab/subqueries` two sessions ago (the Day 11 gap that
was apparently never closed). No nav references remained either (the whole
dead "Lab Demos" dropdown, including its "Joins" link, was already removed
when Analytics replaced it).

### Verified end-to-end (live server, real logins, all 10 Phase F checks)
| Check | Result |
|---|---|
| `/admin/parcels`, no filters | ✅ 35 results (30 seed + 5 from this project's own live-testing sessions), 10/page |
| `?status=DELIVERED` | ✅ 11 results |
| `?tracking=CDB2026` | ✅ 35 results (matches every tracking code's shared prefix) |
| `?tracking=00001` | ✅ exactly 1 result, `CDB202600001` |
| `?weight_min=10&weight_max=25` | ✅ 5 results, matches `SELECT COUNT(*) WHERE weight_kg BETWEEN 10 AND 25` exactly |
| `?sort=weight_kg&dir=desc` | ✅ heaviest (25kg) first, matches `SELECT MAX(weight_kg)` |
| `?page=2` | ✅ different 10 rows than page 1 |
| Admin customers `?active_only=1` | ✅ 13 results, matches the `EXISTS` query run directly |
| Branch mgr `?status=IN_TRANSIT` | ✅ exactly the 6 `IN_TRANSIT` parcels touching branch 1000 (verified by counting status badges, not the stray match from the `<option value="IN_TRANSIT">` in the filter dropdown itself) |
| Rider `?view=completed` | ✅ 4 completed parcels (2 delivered, 2 returned) for rider 1002 |
| `/lab/joins` | ✅ `404` |
| Regression: rider default `active` view, admin parcel show, plain customer list, `/admin/analytics` | ✅ all still 200, unaffected |

## 2026-07-03 — Admin Operations (bulk status tool) + final /lab/* cleanup

### Route collision found before writing any code: `/admin/operations` already existed

`GET /admin/operations` (route name `admin.operations`) was already live —
`ReportsController::operations()`, a real PL/SQL monitoring dashboard that
runs `sp_intransit_monitor`, `sp_weight_violation_scan`, and
`sp_parcel_cost_audit` (the Day 9 audit procedures) and reads their output
back from the `plsql_log` GTT. It is **not** part of the `/lab/*` demo
infrastructure this multi-session cleanup has been removing — it's a
separate, real admin feature that happens to have landed on the same route
name today's prompt wants for the new Bulk Status Tool.

Checked the blast radius before touching anything: `grep -rn "admin.operations\|admin.reports"`
across `resources/views` and `app` returned nothing — neither
`ReportsController::index()` nor `::operations()` is linked from any nav or
any other view. Both pages are fully orphaned (reachable only by typing the
URL directly). Given that, relocated the existing monitoring page rather
than overwrite or silently drop it:
- `admin.operations` (`/admin/operations`) → now `Admin\OperationsController@index`
  (today's new Bulk Status Tool).
- The old `ReportsController::operations()` → moved to `admin.reports.monitors`
  (`/admin/reports/monitors`), same controller method, zero code changes to
  `ReportsController` itself or its view — just a route rename with an
  explanatory comment in `routes/web.php`.
- Verified both routes independently after the change: `/admin/operations`
  (new tool) and `/admin/reports/monitors` (relocated dashboard) both 200,
  no interference.

**Consequence for Phase B**: `database/sql/05-plsql/00-logging-table.sql`
(creates the `plsql_log` GTT) was **not** deleted, and `DROP TABLE plsql_log`
was **not** run — the prompt's own instruction was conditional ("delete ...
if it was only for the lab page ... check if any other code still uses it —
if not, delete"), and it's not: the relocated `admin.reports.monitors` page
still reads from `plsql_log` on every request. Deleting it would have broken
that page today, the same day it was supposedly just "relocated, not
removed."

### Phase A — `bulk_update_stuck_parcels` procedure + Bulk Status Tool

`database/sql/05-plsql/04-bulk-status-update.sql` — compiled clean, **VALID**.
Implements exactly the cursor/exception-handling pattern from the prompt:
`OPEN`/`FETCH`/`EXIT WHEN %NOTFOUND`/`CLOSE` over stuck parcels at a branch,
calling `update_parcel_status` per row inside a nested `BEGIN...EXCEPTION WHEN
OTHERS THEN v_skip_count := v_skip_count + 1; END` block so one row's invalid
transition can't abort the batch.

Verified reading via a cursor while `update_parcel_status` concurrently
reads/writes the same `parcels` table from inside the loop is safe — this is
a standalone procedure, not a row-level trigger, so the `ORA-04091`
mutating-table restriction found in the trigger session doesn't apply here.
Confirmed by running it for real (not just compiling): branch 1004,
threshold 3 days, target `RETURNED` → 2 rows updated, both logged by
`trg_status_history` automatically.

**Design note carried into the UI**: `update_parcel_status`'s state machine
only allows `OUT_FOR_DELIVERY → DELIVERED`, not `IN_TRANSIT → DELIVERED`. The
cursor pulls both `IN_TRANSIT` and `OUT_FOR_DELIVERY` rows, so picking
`DELIVERED` as the bulk target will silently skip any `IN_TRANSIT` rows in
the batch (caught by the procedure's own `WHEN OTHERS`, not a bug) — the form
shows a live warning about this (Alpine-toggled based on the selected radio)
rather than letting the admin discover it from an unexplained lower count.

`Admin\OperationsController` — `index()` (branches dropdown + stuck-parcels
preview query, verified against Oracle before wiring in), `bulkUpdate()`
(validates `branch_id`/`days_threshold > 0`/`new_status` in `RETURNED,DELIVERED`,
calls the procedure via the same raw-PDO OUT-binding pattern as
`book_parcel`/`update_parcel_status`, flashes `"Successfully updated {n} stuck
parcels."`). Deliberately **not** wrapped in `DB::transaction()` — commented
inline why: the procedure already has its own per-row partial-success model
via its internal `WHEN OTHERS` handler, and an outer Laravel transaction
would have nothing left to protect since the procedure is the whole unit of
work.

View: `resources/views/admin/operations/index.blade.php` — stuck-parcels
preview table, branch/threshold/status form, confirm-on-submit dialog
("This will call a PL/SQL cursor procedure. It cannot be undone."), honest
product copy throughout (no "this is Lab 11" language anywhere, per
instruction).

"Operations" added to the admin nav between Analytics and Customers.

### Phase B/C — `/lab/*` final audit

Confirmed (again) that none of `/lab/plsql`, `/lab/procedures`,
`app/Http/Controllers/Lab/`, or `resources/views/lab/` exist anywhere in this
codebase — same finding as the last two sessions for `/lab/joins`,
`/lab/aggregates`, `/lab/subqueries`. All 5 `/lab/*` routes this multi-session
cleanup has now checked for return `404`. `_demo_block.blade.php` doesn't
exist either. Nothing to delete in Phase C beyond what was already gone.

### Phase D — Navigation

Admin sidebar already had no "Lab Demos" section (removed when Analytics
replaced it two sessions ago) — confirmed via grep across all 4 layout
components (`admin`, `branch`, `rider`, `customer`) before touching anything;
all already clean of `/lab/*` references. Only change needed was adding the
new "Operations" link.

### Phase E — Transaction audit (all 4 already correct, no changes needed)
1. `Admin\ParcelController@store` — `book_parcel` call in try/catch, no
   `DB::transaction()` wrapper, nothing runs after the procedure except a
   read-only `SELECT` to resolve the redirect target. Already matches the
   "single atomic call, no wrapping needed" guidance exactly.
2. `Customer\ParcelController@store` — same pattern, confirmed identical.
3. `Admin\OperationsController@bulkUpdate` — built today with the explicit
   "don't wrap" comment, as above.
4. `Rider\DeliveryController@logAttempt` — already wraps the
   `delivery_attempts` INSERT + conditional `update_parcel_status` call in
   `DB::transaction()` with an outer try/catch that surfaces any
   `PDOException` (including `ORA-20006`) via `back()->withErrors(...)`,
   rendered by the view's existing generic `$errors->any()` block. This is
   slightly more than the prompt asked for (a transaction wrapper in
   addition to the try/catch) but strictly safer, not a conflict — left as
   is rather than removed to match the letter of "no explicit transaction
   needed."

### Verified end-to-end (live server, real logins)
| Check | Result |
|---|---|
| `/admin/operations` loads, preview populated | ✅ 200, real stuck-parcel data from seed |
| Bulk update: branch 1003, 1-day threshold, `RETURNED` | ✅ flash "Successfully updated 2 stuck parcels.", both parcels now `RETURNED`, `parcel_status_history` gained rows via `trg_status_history` |
| `/lab/plsql`, `/lab/procedures` | ✅ both `404` |
| Admin sidebar | ✅ "Operations" and "Analytics" present, zero "Lab Demos" text anywhere |
| Branch manager dashboard | ✅ zero `/lab/*` links |
| Rider logs a successful attempt against an already-`RETURNED` parcel | ✅ `200`, red banner "Parcel is in a terminal state: RETURNED", no crash |
| Regression: `/admin/reports/monitors` (relocated page) | ✅ 200, still reads `plsql_log` correctly |
| Regression: `/admin/operations` (new tool, not the old page) | ✅ 200, no route collision |

### Known gaps
1. `get_parcel_status` and `assign_rider` remain the only 2 of the original 5
   PL/SQL functions/procedures from two sessions ago still unused by any
   controller.
2. The relocated `/admin/reports/monitors` and `/admin/reports` pages are
   still not linked from any nav — they were already orphaned before today,
   and today's task didn't ask for that to change, so it wasn't addressed.

## 2026-07-03 — Final polish and integration testing (no new features)

Full end-to-end audit of all 4 roles against a live server, final Oracle
object verification, a real integration smoke test, and a UI consistency
pass. Two real bugs fixed (one product gap, one dead code path); everything
else confirmed already correct from prior sessions.

### Phase A — Per-role audit, live server + real logins (not just code review)

**Admin** — dashboard stats matched direct DB counts exactly (35 parcels, 6
in transit, 11 active riders). All 3 analytics sub-pages, the operations
bulk tool, and the filtered/paginated parcel list all loaded and returned
correct data. `book_parcel` → `update_parcel_status` → `trg_status_history`
chain verified with a real booking (parcel 1307) and a real status change.

**Bug found and fixed**: the admin parcel-create form's `assigned_rider_id`
dropdown has existed since the parcel-booking work but was never actually
wired to anything — `book_parcel` has no rider parameter, so selecting a
rider silently did nothing (documented as a known gap in an earlier session,
but never revisited). This is dead UI, not a missing feature, so fixing it
is in-scope for "final polish": `Admin\ParcelController@store` now calls
`assign_rider` as a second step after a successful booking when a rider was
selected. Since the parcel is already booked by that point, a failed
assignment (e.g. an inactive rider) flashes a **warning**, not a full-page
error, and the booking itself is never rolled back. Verified live: the
create form's dropdown only ever offers `active_flag='Y'` riders (so it
can't produce this case through the UI itself), but a direct POST with a
tampered `assigned_rider_id=1007` (inactive) correctly surfaced "Rider could
not be assigned: Cannot assign an inactive rider (rider_id=1007)" alongside
"Parcel booked successfully." — both flashes rendering together, parcel
left with `assigned_rider_id = NULL`, no 500.

**Branch Manager** — dashboard total (20) matched a direct branch-scoped
count exactly. Filtered list, 403 on another branch's parcel, and both a
rejected (`BOOKED → DELIVERED`) and accepted (`BOOKED → IN_TRANSIT`)
transition all verified live.

**Rider** — Active/Completed toggle, 403 on a non-assigned parcel, and the
terminal-state flash error all verified live. Successful delivery and the
3-strikes auto-return chain both verified live too, but only after a test
setup correction: the first delivery attempt was tried against an
`IN_TRANSIT` parcel and correctly rejected with `ORA-20007` ("Invalid
status: DELIVERED") — `update_parcel_status` only allows `DELIVERED` from
`OUT_FOR_DELIVERY`, not `IN_TRANSIT`, exactly as documented two sessions
ago. Not a bug; a reminder that the rider dashboard's "Active Jobs" list
mixes both statuses under one "Log Delivery Attempt" button (see Known
Limitations below).

**Customer** — dashboard scoping, booking (`trg_auto_fee` confirmed firing),
status timeline, 403 on another customer's parcel, `customer_total_spend`,
and quick-track scoping all verified live.

### Phase B — Final Oracle object verification

`SELECT object_name, object_type, status FROM user_objects WHERE
object_type IN ('PROCEDURE','FUNCTION','TRIGGER','SEQUENCE')` — every object
in the expected list is `VALID`/`ENABLED`, exactly matching counts:

| Type | Expected | Found |
|---|---|---|
| PROCEDURE | `book_parcel`, `assign_rider`, `update_parcel_status`, `bulk_update_stuck_parcels` (4) | ✅ all 4, plus 3 legacy Day 9 audit procedures (`sp_intransit_monitor`, `sp_weight_violation_scan`, `sp_parcel_cost_audit`) still in use by the relocated `/admin/reports/monitors` page |
| FUNCTION | `calculate_fee`, `get_parcel_status`, `rider_success_rate`, `customer_total_spend`, `branch_revenue` (5) | ✅ all 5 |
| TRIGGER | `trg_status_history`, `trg_auto_fee`, `trg_auto_return`, `trg_rider_active` (4) | ✅ all 4, plus Laravel's own identity-column triggers on `users`/`jobs`/etc. |
| SEQUENCE | `seq_customer_id` ... `seq_fee_id` (8) | ✅ all 8, plus Laravel's own framework sequences |

### Phase C — Integration smoke test (`database/sql/10-integration-test/01-smoke-test.sql`)

Written fresh as a real regression test, not a lab demo. **Two bugs found
in the prompt's own script before it would even compile/pass**, both fixed:

1. `ORA-06550/PLS-00103` on `update_parcel_status((SELECT parcel_id FROM
   parcels WHERE tracking_code = v_tc), ...)` — a bare `(SELECT ...)`
   subquery is valid as a procedure argument *inside a SQL statement*, but
   not as a plain PL/SQL procedure-call argument; those are two different
   parsers. Fixed by resolving `v_pid` once via `SELECT ... INTO` right
   after booking, and reusing it everywhere instead of repeating the inline
   subquery.
2. `assert('book_parcel returns tracking code', ... LENGTH(v_tc) = 14)` —
   the tracking code format is `'CDB' || YYYY || LPAD(id,5,'0')` = 3+4+5 =
   **12** characters, not 14. Confirmed against real generated codes
   (`CDB202601309`, etc.) before fixing the assertion. Left as `LENGTH(v_tc)
   = 12`; a genuinely correct `book_parcel` call would otherwise register
   as a false `FAIL`.

Ran clean after both fixes: **10 passed, 0 failed**. Confirmed the `ROLLBACK`
at the end leaves zero residue — `SELECT COUNT(*) FROM parcel_status_history
WHERE changed_by = 'SMOKE_TEST'` and the equivalent for `delivery_attempts`
both return 0 after running it.

Deliberately **not** added to `run-all.sql`'s automatic include chain:
sequence `NEXTVAL` consumption isn't transactional in Oracle, so the
`ROLLBACK` cleans up the rows but not the burned sequence values — running
it on every setup would silently eat into `seq_parcel_id`, `seq_attempt_id`,
etc. every time. `run-all.sql` documents the manual command instead.

### Phase D — Nav, error handling, and status badges

- All 4 layouts confirmed free of `/lab/*` references and "Lab"/"demo"/
  "showcase" text (already true from prior sessions), and all use
  `routeIs()` for active-link highlighting.
- **Fixed**: the rider layout showed no user name or role anywhere in its
  UI — the only one of the 4 layouts missing this. Added name + a "Rider"
  badge to the header. Also added a small role badge (`Admin`, `Customer`,
  `Branch Mgr`) next to the name in the other 3 layouts for consistency —
  previously only the branch layout spelled out the role explicitly; admin
  and customer relied on the brand text alone.
- The `oracleErrorMessage()` ORA- extraction pattern was already present
  and identical across all 5 controllers that call a procedure (`Admin\ParcelController`,
  `Customer\ParcelController`, `Branch\ParcelController`, `Rider\DeliveryController`,
  `Admin\OperationsController`) — confirmed, no changes needed.
- **New**: `resources/views/components/status-badge.blade.php` — single
  source of truth for parcel status colors, `@props(['status', 'size' =>
  'sm'])`. Replaced hardcoded status-color arrays/spans in **13 views**
  (`admin/dashboard`, `admin/analytics/parcels`, `admin/operations/index`,
  `admin/parcels/index`, `admin/parcels/show`, `branch/dashboard`,
  `branch/parcels/index`, `branch/parcels/show`, `customer/dashboard`,
  `customer/parcels/index`, `customer/parcels/show`, `reports/index`,
  `rider/dashboard`) plus the shared `partials/parcel-timeline` — every one
  of them had its own copy-pasted `$statusColors`/`$colors` array, several
  already drifted from each other. The `size="lg"` prop exists because
  merging a caller-supplied `text-sm`/`px-3` class onto the component's
  default `text-xs`/`px-2.5` is unreliable — Tailwind utility classes share
  CSS specificity, so which one wins depends on stylesheet generation
  order, not HTML attribute order. A controlled prop avoids that risk
  entirely rather than relying on `$attributes->merge()` for anything that
  touches padding/font-size.
- **Color mapping changed** as part of centralizing: this task's spec
  (`BOOKED=gray, IN_TRANSIT=blue, OUT_FOR_DELIVERY=orange, DELIVERED=green,
  RETURNED=red`) differs from what every view had used until today
  (`BOOKED=blue, IN_TRANSIT=yellow`). Followed today's explicit spec since
  centralizing into one component is the natural point to normalize a
  scheme that had already drifted inconsistently across files anyway.
  Verified live: `?status=BOOKED` renders gray, `?status=IN_TRANSIT` renders
  blue, consistently across pages.
- Regression-tested every page touched by the badge swap across all 4 roles
  after the refactor — all still 200, no visual/logic breakage.

### Verified end-to-end (Phase A–D combined, live server)
| Check | Result |
|---|---|
| All 4 roles' full checklists (see Phase A above) | ✅ pass, 2 real fixes applied |
| `SELECT ... user_objects` (Phase B) | ✅ all VALID/ENABLED, exact counts match |
| Smoke test (Phase C) | ✅ 10/10 passed after 2 script fixes, clean rollback confirmed |
| All 4 layouts: no `/lab/*`, no "Lab"/"demo" text, name+role visible | ✅ |
| Status badges: 13 views + 1 partial migrated to `<x-status-badge>` | ✅ new gray/blue scheme renders correctly everywhere, no regressions |

---

## Final State (as of 2026-07-03)

**Oracle objects** (business-logic only, excludes Laravel framework
identity sequences/triggers on `users`/`jobs`/`cache`/etc.):
- 4 procedures: `book_parcel`, `assign_rider`, `update_parcel_status`, `bulk_update_stuck_parcels`
- 5 functions: `calculate_fee`, `get_parcel_status`, `rider_success_rate`, `customer_total_spend`, `branch_revenue`
- 4 triggers: `trg_status_history`, `trg_auto_fee`, `trg_auto_return`, `trg_rider_active`
- 8 sequences: `seq_customer_id`, `seq_receiver_id`, `seq_branch_id`, `seq_rider_id`, `seq_parcel_id`, `seq_history_id`, `seq_attempt_id`, `seq_fee_id`
- **21 total**, all `VALID`/`ENABLED` as of the Phase B check above.
- Plus 3 legacy Day 9 audit procedures (`sp_intransit_monitor`,
  `sp_weight_violation_scan`, `sp_parcel_cost_audit`) still live and in use
  by `/admin/reports/monitors`, not counted in the 21 above since they
  predate this project's procedure/function/trigger buildout.

**Routes**: `php artisan route:list` → 75 distinct routes (79 lines
including the table header/border in raw `wc -l` output).

**Views**: `find resources/views -name "*.blade.php" | wc -l` → 69 Blade files.

**Business-concept coverage** (each SQL concept now lives inside a real
product feature, not a labeled demo — the last of the original `/lab/*`
pages was removed 3 sessions ago):

| Concept | Where it actually lives |
|---|---|
| Joins (inner/outer/self) | Admin parcel list (6-way join: customers, receivers, branches ×2 self-joined, riders, fees), admin customer search, branch-scoped parcel queries |
| Aggregates + `GROUP BY`/`HAVING` | Analytics section (Branch Performance, Rider Performance, Parcel Intelligence sub-pages), Operations stuck-parcel preview |
| Subqueries (correlated + `EXISTS`) | Admin customers' "Has active parcels" filter, underperforming-branches detection (Analytics), stuck-parcel detection (`SYSDATE - booked_at`), top-rider-by-attempts (Analytics overview) |
| Transactions / atomic multi-step writes | Parcel booking (`book_parcel` procedure + `trg_auto_fee` + manual initial history row, one atomic call from PHP) |
| PL/SQL (cursors, exception handling, %ROWTYPE, arithmetic operators) | All 4 triggers, all 4 procedures (`bulk_update_stuck_parcels`'s cursor + nested exception handler is the most complete single example), 5 functions, and the integration smoke test |
| Constraints | Oracle-level: `CHECK`/`UNIQUE` constraints from Day 4 (`chk_parcel_status`, `chk_parcel_weight`, `uq_customer_phone`, etc.). Application-level: `trg_rider_active` (can't assign an inactive/nonexistent rider — a protection with no equivalent CHECK constraint possible, since it depends on another table's current data) |

**Known limitations (honest)**:
1. **Rider dashboard's "Active Jobs" mixes two statuses that aren't
   equally valid for delivery.** `IN_TRANSIT` and `OUT_FOR_DELIVERY`
   parcels both show the same "Log Delivery Attempt" → "Delivered" button,
   but `update_parcel_status`'s state machine only allows `DELIVERED` from
   `OUT_FOR_DELIVERY`. Marking an `IN_TRANSIT` parcel delivered fails
   cleanly (flash error, not a crash — confirmed today), but there's no
   rider-facing action to move a parcel from `IN_TRANSIT` to
   `OUT_FOR_DELIVERY` in the first place. In a real courier workflow that
   transition would probably happen automatically (arriving at the final
   branch) or via a rider action ("I have it, starting delivery") that
   doesn't exist yet. Not fixed today — it's new-feature scope, not polish.
2. **`get_parcel_status` and `assign_rider` are the only 2 of the original
   PL/SQL objects still not called from anywhere in the app.**
   `assign_rider` is now used (today's Phase A fix), leaving just
   `get_parcel_status` genuinely unused.
3. **`changed_by` on every trigger-logged `parcel_status_history` row reads
   `CDB_ADMIN`** (the Oracle session user), not the acting app user, since
   `trg_status_history` uses `USER` per its original spec. The initial
   `BOOKED` row (inserted manually by `book_parcel`) is the only one that
   still shows the real app username.
4. **`/admin/reports` and `/admin/reports/monitors` are not linked from any
   navigation** — both predate this project's role-based nav work and have
   been orphaned (reachable only by direct URL) the whole time; not
   addressed since no session's scope has asked for it.
5. **This is a single-admin-user Oracle connection model** — every Laravel
   request connects as `cdb_admin` regardless of which app user is logged
   in, which is the root cause of limitation #3 and means Oracle-level
   auditing (`user_objects`, session-level tracking) can't distinguish
   which app user did what — only the application's own `parcel_status_history.changed_by`
   (where manually populated) and Laravel's own auth/session logs can.

---

## 2026-07-09 — Full Frontend Redesign (no backend changes)

Full UI redesign applied — design system documented in docs/PROGRESS.md.
All pages use role-specific layout components. Status badge component
updated. No lab-demo styling anywhere.

**Scope discipline**: this session touched only Blade view files (HTML
structure + Tailwind classes) and 3 pure-presentation atom components
(`nav-link`, `responsive-nav-link`, `dropdown-link`). Zero controllers,
routes, migrations, or SQL files were modified — verified by re-reading
every PHP variable each view consumed before rewriting, so every
`compact(...)`/`view(...)` payload from every controller is unchanged.

**Design system**: indigo-600 primary, amber-500 accent, slate-* neutrals
replacing the old per-role demo palette (admin was already slate/indigo
from a prior session; branch manager's teal, rider's plain slate-900, and
customer's orange were all replaced). Status colors standardized:
BOOKED=slate, IN_TRANSIT=blue, OUT_FOR_DELIVERY=amber, DELIVERED=emerald,
RETURNED=red, applied consistently via `<x-status-badge>` everywhere a
parcel status is shown, including inside the shared
`partials/parcel-timeline.blade.php`.

**What changed, by area**:
- **Admin**: analytics sub-pages (branches/riders/parcels — riders gained
  the "Attempts" column that had been missed on first pass), operations
  bulk-update tool (now two-column: stuck-parcel preview + amber-accented
  action form), and the customers/branches/riders index+show pages, which
  had been left on pure Breeze defaults since Day 11 despite the rest of
  admin being redesigned.
- **Branch manager**: `branch-layout` rebuilt as a white sidebar (was a
  teal-themed shell distinct from every other role), dashboard, parcel
  list, and parcel detail restyled to match.
- **Rider**: `rider-layout`'s dark mobile header and bottom nav restyled
  from slate-900 accents to indigo-600 to match the system; dashboard and
  delivery-attempt-log page (large radio outcome cards) restyled.
- **Customer**: `customer-layout` rebuilt as a white sidebar with a
  left-border active-link accent (was orange-themed); dashboard, parcel
  list (added client-side Alpine status-filter pills — the controller
  returns all of a customer's parcels unfiltered with no `status` query
  param, so server-side filtering wasn't an option without touching the
  controller), booking form (origin→destination arrow layout), parcel
  tracking page (progress stepper), and receivers index (card grid with a
  dashed "Add Address" tile) + create form.
- **Shared/legacy shell**: `layouts/app.blade.php` + `layouts/navigation.blade.php`
  (used only by `/profile`, which is reachable by all 4 roles, plus the
  dead-code `resources/views/dashboard.blade.php` — the `/dashboard` route
  is a closure that always redirects via `RoleRedirect`, so that view
  never actually renders) restyled from gray/yellow/orange to
  slate/indigo/amber/blue/emerald for consistency, along with the
  `nav-link`, `responsive-nav-link`, and `dropdown-link` atoms it uses.
  `errors/access-denied.blade.php` moved from gray-* to slate-*.
- **`/admin/reports` and `/admin/reports/monitors`**: switched from
  `<x-app-layout>` (a leftover Breeze-default leak) to `<x-admin-layout>`
  and restyled to match. Left unlinked from any nav, per the existing
  documented decision (limitation #4 above / the comment in `web.php`
  explaining `reports.monitors` was deliberately never linked) — still
  reachable by direct URL, now visually consistent with the rest of admin.

**Adaptations where the real data model didn't match the literal design
brief** (documented rather than fabricated):
- Branch parcel list's "Direction: Outgoing/Incoming" pill was dropped.
  `Branch\ParcelController::index()` selects `origin_city`/`destination_city`
  (text) but not `origin_branch_id`/`destination_branch_id`, and city name
  isn't a reliable proxy for "is this branch the origin" (multiple branches
  can share a city) — computing it client-side would have been a guess,
  not a fact, without editing the controller.
- Customer parcel list's "status filter pills" are client-side (Alpine
  `x-show`), not server-side, for the reason above.
- Admin dashboard's status breakdown (from Day 11, unchanged today) stays
  a dropdown + single count rather than a full bar-per-status chart, since
  the controller only ever hands the view one `$selectedStatus`/`$statusCount`
  pair, not a full array.

**Verified end-to-end** (live server, real logins as all 4 test accounts —
`admin@test.com`, `branchmgr@test.com`, `rider@test.com`, `customer@test.com`,
password `password`):

| Check | Result |
|---|---|
| All 4 roles log in and land on their dashboard | ✅ 200, correct redirect target |
| Every admin page (dashboard, parcels index+show, analytics ×4, operations, customers/branches/riders index+show, reports, reports/monitors, profile) | ✅ 200, no PHP errors, new design classes present |
| Branch dashboard + parcel index | ✅ 200, no errors |
| Rider dashboard (active + completed tabs) | ✅ 200, no errors |
| Customer dashboard, parcels index/create, receivers index/create | ✅ 200, no errors |
| `grep` sweep: no `<x-app-layout>` outside `/profile` + dead-code `dashboard.blade.php` | ✅ |
| `grep` sweep: no "Lab Demo" text anywhere | ✅ |
| `grep` sweep: no duplicate local `session('success'/'error')` blocks left in `admin/*` views (now centralized in `admin-layout`) | ✅ |
| `grep` sweep: no leftover `text-gray-*`/`bg-gray-*`/`border-gray-*` in `admin/`, `branch/`, `rider/`, `customer/` view trees | ✅ |
| `grep` sweep: every raw parcel-status output goes through `<x-status-badge>` | ✅ 14 files use it, no bare `{{ $x->current_status }}` found | 
| `npm run build` | ✅ clean, 5.14s, no warnings |
