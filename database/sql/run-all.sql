
DEFINE base = 'e:\xampp\htdocs\Courier-Parcel-Tracking-Delivery-Management-System--Database-Project\database\sql'


@&base.\01-setup\01-create-users.sql
@&base.\01-setup\02-create-roles.sql
@&base.\01-setup\03-grant-privileges.sql


@&base.\02-schema\01-sequences.sql
@&base.\02-schema\02-customers.sql
@&base.\02-schema\03-receivers.sql
@&base.\02-schema\04-branches.sql
@&base.\02-schema\05-riders.sql
@&base.\02-schema\06-parcels.sql
@&base.\02-schema\07-parcel-status-history.sql
@&base.\02-schema\08-delivery-attempts.sql
@&base.\02-schema\09-fees.sql

-- Day 4: constraints, column modifications, seed data
@&base.\02-schema\10-alter-constraints.sql
@&base.\02-schema\11-alter-modify.sql
@&base.\03-seed\01-customers.sql
@&base.\03-seed\02-receivers.sql
@&base.\03-seed\03-branches.sql
@&base.\03-seed\04-riders.sql
@&base.\03-seed\05-parcels.sql
@&base.\03-seed\06-parcel-status-history.sql
@&base.\03-seed\07-delivery-attempts.sql
@&base.\03-seed\08-fees.sql


@&base.\05-plsql\00-logging-table.sql
@&base.\05-plsql\01-block-structure.sql
@&base.\05-plsql\02-exception-handling.sql
@&base.\05-plsql\03-cursor-intro.sql

-- Day 10: table-level grants for branch_mgr/rider/customer roles (as SYSTEM)
@&base.\01-setup\04-table-grants.sql

-- Day 12: customer account linking (as cdb_admin)
@&base.\02-schema\13-alter-customers-user-link.sql

-- Day 13: rider account linking (as cdb_admin)
@&base.\02-schema\12-alter-riders-user-link.sql

-- Procedures & Functions (as cdb_admin) — functions first, procedures depend on them
@&base.\07-functions\01-calculate-fee.sql
@&base.\07-functions\02-get-parcel-status.sql
@&base.\07-functions\03-rider-success-rate.sql
@&base.\07-functions\04-customer-total-spend.sql
@&base.\07-functions\05-branch-revenue.sql
@&base.\06-procedures\01-book-parcel.sql
@&base.\06-procedures\02-assign-rider.sql
@&base.\06-procedures\03-update-status.sql
