# CourierDB — Courier & Parcel Tracking / Delivery Management System

A full-stack courier operations platform built on **Laravel 12** + **Oracle Database 11g XE**. It's a database-systems course project first and a web app second: the goal is to demonstrate PL/SQL (stored procedures, functions, triggers, cursors, exception handling) actually driving a real, role-based application — not sitting isolated as classroom exercises. Every business rule that must never be bypassed (fee calculation, status-transition validity, auto-return after failed deliveries, rider eligibility) lives in the database as PL/SQL, not in PHP.

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12, PHP 8.2 |
| Database driver | [`yajra/laravel-oci8`](https://github.com/yajra/laravel-oci8) (PDO/OCI8) |
| Database | Oracle Database 11g Express Edition |
| Frontend | Blade templates, Tailwind CSS, Alpine.js, bundled with Vite |
| Auth | Laravel session auth (Breeze scaffolding) + a custom `role` column for RBAC |

The app deliberately bypasses Eloquent for all business tables — every query is raw `DB::select`/`DB::insert`/`DB::statement`, and every write that has a business rule attached goes through a PL/SQL procedure via a `BEGIN ... END;` PDO call. `users`, `sessions`, `cache`, and `jobs` are the only Laravel-migration-managed tables; everything else (`customers`, `receivers`, `branches`, `riders`, `parcels`, `parcel_status_history`, `delivery_attempts`, `fees`) is raw SQL DDL under `database/sql/`.

## Roles & Features

Four portals share one Oracle schema, gated by `users.role` (`admin` | `branch_mgr` | `rider` | `customer`) via `EnsureUserHasRole` middleware and routed by `App\Support\RoleRedirect`.

**Admin** (`/admin/*`)
- Dashboard — parcel totals, in-transit count, today's revenue, active rider count, recent bookings filterable by date range
- Analytics — overview, branch performance, rider performance, parcel funnel/weight-band breakdowns
- Customers / Branches / Riders — full CRUD
- Parcels — search/filter/paginate, book a new parcel (with optional rider assignment), view timeline + fee + delivery attempts, manually transition status
- Operations — bulk-sweep parcels stuck past a day threshold to `RETURNED`/`DELIVERED`, scoped per branch

**Branch Manager** (`/branch/*`)
- Dashboard + parcel list scoped to their own branch (origin or destination only — enforced server-side, not just hidden in the UI)
- View parcel detail, update status

**Rider** (`/rider/*`)
- Dashboard — active jobs / completed jobs, today's delivered/failed counts
- Log a delivery attempt (success or failure + reason) for a parcel assigned to them

**Customer** (`/customer/*`)
- Dashboard — status breakdown, total spend, 5 most recent parcels
- Book a parcel, manage saved receivers, view parcel history, track a parcel by tracking code

Self-registration (`/register`) always creates a `customer` account and auto-links (or creates) a matching `customers` row by email. `admin` / `branch_mgr` / `rider` accounts are not self-serviceable — see [Creating role-specific accounts](#creating-role-specific-accounts) below.

## Database Objects Reference

| Object | Type | File | Called from |
|---|---|---|---|
| `book_parcel` | Procedure | `06-procedures/01-book-parcel.sql` | Admin & Customer "Book a Parcel" forms |
| `assign_rider` | Procedure | `06-procedures/02-assign-rider.sql` | Admin "Book a Parcel" form (optional rider dropdown) |
| `update_parcel_status` | Procedure | `06-procedures/03-update-status.sql` | Admin/Branch parcel show page + Rider delivery log (on success) |
| `bulk_update_stuck_parcels` | Procedure | `05-plsql/04-bulk-status-update.sql` | Admin → Operations bulk-update form |
| `calculate_fee` | Function | `07-functions/01-calculate-fee.sql` | Called only from `trg_auto_fee`, not directly from PHP |
| `get_parcel_status` | Function | `07-functions/02-get-parcel-status.sql` | Not called anywhere — only exercised by the smoke test |
| `rider_success_rate` | Function | `07-functions/03-rider-success-rate.sql` | Admin Analytics (overview + rider performance) |
| `customer_total_spend` | Function | `07-functions/04-customer-total-spend.sql` | Customer dashboard |
| `branch_revenue` | Function | `07-functions/05-branch-revenue.sql` | Admin Analytics (branch performance) |
| `trg_status_history` | Trigger | `08-triggers/01-trg-status-history.sql` | Fires on every `current_status` change; logs `parcel_status_history` |
| `trg_auto_fee` | Trigger | `08-triggers/02-trg-auto-fee.sql` | Fires on parcel insert; auto-creates the `fees` row |
| `trg_auto_return` | Trigger | `08-triggers/03-trg-auto-return.sql` | Fires on delivery-attempt insert; auto-returns after 3 failures |
| `trg_rider_active` | Trigger | `08-triggers/04-trg-rider-active.sql` | Fires on rider assignment; blocks assigning an inactive rider |

The parcel status state machine is: `BOOKED → IN_TRANSIT → OUT_FOR_DELIVERY → DELIVERED`, with `RETURNED` reachable from any non-terminal state — enforced entirely inside `update_parcel_status`, not in PHP.

## Project Structure

```
app/Http/Controllers/
  Admin/        Admin portal (Dashboard, Analytics, Customers, Branches, Riders, Parcels, Operations)
  Branch/       Branch manager portal
  Rider/        Rider portal
  Customer/     Customer portal
database/
  migrations/   Laravel-managed tables only: users, sessions, cache, jobs
  sql/          Everything else — the real business schema, seed data, and PL/SQL
    01-setup/       Oracle users (cdb_admin, cdb_branch_mgr, cdb_rider, cdb_customer) + grants
    02-schema/       Table DDL for the 8 core business tables
    03-seed/         Demo data
    05-plsql/        Standalone PL/SQL blocks (bulk status sweep)
    06-procedures/   book_parcel, assign_rider, update_parcel_status
    07-functions/    calculate_fee, get_parcel_status, rider_success_rate, customer_total_spend, branch_revenue
    08-triggers/     trg_status_history, trg_auto_fee, trg_auto_return, trg_rider_active
    10-integration-test/  Regression smoke test — run after touching any procedure/function/trigger
    run-all.sql      Runs everything above in dependency order
resources/views/    Blade templates, one directory per portal (admin/, branch/, rider/, customer/)
routes/web.php      All route definitions, grouped by role prefix + middleware
```

## Setup

### Prerequisites
- PHP 8.2, Composer
- Node.js + npm
- Oracle Database 11g XE (or compatible) with the OCI8 PHP extension and an Instant Client matching your PHP build

### 1. Install dependencies
```bash
composer install
npm install
```

### 2. Configure the environment
```bash
cp .env.example .env
php artisan key:generate
```
Edit `.env` for Oracle:
```
DB_CONNECTION=oracle
DB_HOST=localhost
DB_PORT=1521
DB_DATABASE=XE
DB_USERNAME=cdb_admin
DB_PASSWORD=<your password>
```

### 3. Build the Oracle schema
Edit the `DEFINE base` path at the top of `database/sql/run-all.sql` to point at your local checkout, then run it as a privileged Oracle user (e.g. `SYSTEM`):
```
sqlplus system/<password>@XE @database/sql/run-all.sql
```
This creates the four Oracle role users, the 8 core business tables, seed data, and every procedure/function/trigger in the reference table above. After any later change to a procedure/function/trigger, run the regression test by hand:
```
sqlplus cdb_admin/<password>@XE @database/sql/10-integration-test/01-smoke-test.sql
```

### 4. Create Laravel's own tables
```bash
php artisan migrate
php artisan db:seed
```
This creates `users`/`sessions`/`cache`/`jobs` and one generic `test@example.com` user — it does not create role-specific demo accounts.

### 5. Build assets and run
```bash
npm run dev      # or: npm run build
php artisan serve
```

### Creating role-specific accounts
Registering via `/register` always creates a `customer` account. To test the other portals, register normally, then promote the account directly in the database:
```sql
-- Admin
UPDATE users SET role = 'admin' WHERE email = 'you@example.com';

-- Branch manager (branch_id must match an existing branches.branch_id)
UPDATE users SET role = 'branch_mgr', branch_id = 1000 WHERE email = 'you@example.com';

-- Rider (also link the riders row so the portal can resolve it)
UPDATE users SET role = 'rider' WHERE email = 'you@example.com';
UPDATE riders SET user_id = (SELECT id FROM users WHERE email = 'you@example.com') WHERE rider_id = 1000;
```

## Known Limitations

- **Fees never get marked paid.** `trg_auto_fee` always inserts `paid_flag = 'N'`, and nothing in the app ever flips it to `'Y'` — so `customer_total_spend` (which only sums paid fees) will always read 0 on unmodified data.
- **No UI to (re)assign a rider after booking.** `assign_rider` can only be triggered from the initial "Book a Parcel" form; the parcel show page displays the assigned rider read-only.
- **`get_parcel_status` is dead code** — defined but never called from the app.
- **Rider delivery logging doesn't gate on parcel state.** A rider can open the delivery log for any active job and select "Delivered Successfully" even if the parcel hasn't reached `OUT_FOR_DELIVERY` yet; `update_parcel_status` correctly rejects the transition, but the rejection surfaces as a raw Oracle error message rather than the UI hiding the option beforehand.
