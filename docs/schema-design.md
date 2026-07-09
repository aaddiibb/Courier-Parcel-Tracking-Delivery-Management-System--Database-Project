# Schema Design

## Application-layer associations

The core business schema is the 8 tables created by the raw SQL scripts under
`database/sql/02-schema/` (customers, receivers, branches, riders, parcels,
parcel_status_history, delivery_attempts, fees), owned by the `cdb_admin`
Oracle schema.

`users` is a separate table, created and versioned by Laravel migrations
(`database/migrations/`), not part of that 8-table set. It exists purely for
application authentication/authorization and is not represented in the ER
diagram under `schema/`.

- `users.role` (added by `2026_05_23_173829_add_role_to_users_table.php`) —
  `admin` | `branch_mgr` | `rider` | `customer`.
- `users.branch_id` (added by `2026_07_03_030828_add_branch_id_to_users_table.php`)
  — nullable, set only for `branch_mgr` users. Logically references
  `branches.branch_id`. No DB-level FK constraint by choice — not a
  cross-schema limitation (`users` and `branches` are both physically in the
  `cdb_admin` Oracle user; `customers.user_id` below proves a real FK across
  that boundary is possible), just a deliberate call to skip it for a
  role-scoping-only column managed on the Laravel side.
- `riders.user_id` (added by `database/sql/02-schema/12-alter-riders-user-link.sql`,
  raw SQL rather than a migration since `riders` is a raw-SQL-managed table)
  — nullable, logical link to `users.id` for `rider` role scoping. No FK
  constraint, same reasoning as `users.branch_id` above. Test row: `rider@test.com`
  (`users.id = 44`) linked to `riders.rider_id = 1002` (Mahfuzur Rahman).
- `customers.user_id` (added by `13-alter-customers-user-link.sql`, Day 12)
  — for contrast, this one **does** have a real FK + UNIQUE constraint to
  `users.id`, since customer self-service account linking benefits from DB-level
  integrity in a way the internal role-scoping columns above don't.

`Branch/*` controllers read `auth()->user()->branch_id` and filter every
parcel query by `origin_branch_id = :id OR destination_branch_id = :id`, so a
branch manager only ever sees parcels touching their own branch. `Branch/ParcelController::show`
and `::updateStatus` re-check branch ownership server-side before returning
data or applying an update, even for a parcel ID that exists — this is the
authorization boundary, not just a UI filter.

`Rider/*` controllers resolve `rider_id` from `riders.user_id = auth()->user()->id`
and filter parcels by `assigned_rider_id = :rider_id`. `Rider/DeliveryController::logAttempt`
re-checks that the parcel's `assigned_rider_id` matches before accepting a
delivery attempt, same ownership-check pattern as the branch manager scoping.

### No DB triggers/procedures for status transitions (yet)

There is no `update_parcel_status` procedure and no status-change trigger in
the database — `database/sql/02-schema/07-parcel-status-history.sql` explicitly
defers that trigger to a later day. Every controller that changes
`parcels.current_status` (`Admin\ParcelController`, `Branch\ParcelController`,
`Rider\DeliveryController`) does it the same way: a plain `DB::update` on
`parcels` plus a manual `DB::insert` into `parcel_status_history` in the same
transaction. This includes the "3 failed delivery attempts auto-returns the
parcel" rule — implemented as an app-layer check in `Rider\DeliveryController`
(count failed `delivery_attempts` for the parcel, transition to `RETURNED` at
3), not a `trg_auto_return` DB trigger, to stay consistent with the rest of
the app until that PL/SQL layer is actually built.
