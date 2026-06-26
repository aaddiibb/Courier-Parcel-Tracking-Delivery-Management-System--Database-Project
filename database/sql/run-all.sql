-- run-all.sql
-- Master script. Can be run from any directory in SQL*Plus or SQL Developer.
-- Day 2 (as SYSTEM): sqlplus system/<pw>@XE @"<full path>\run-all.sql"
-- Day 3+ (as cdb_admin): sqlplus cdb_admin/<pw>@XE @"<full path>\run-all.sql"
--
-- Update base to the absolute path of this file's directory if you move the project.
DEFINE base = 'e:\xampp\htdocs\Courier-Parcel-Tracking-Delivery-Management-System--Database-Project\database\sql'

-- Day 2: users, roles, privileges (requires SYSTEM)
@&base.\01-setup\01-create-users.sql
@&base.\01-setup\02-create-roles.sql
@&base.\01-setup\03-grant-privileges.sql

-- Day 3: sequences and business tables (as cdb_admin)
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

-- Day 9: PL/SQL — session logging table + stored procedures (as cdb_admin)
-- sp_intransit_monitor  : explicit cursor, %ROWTYPE, SYSDATE arithmetic
-- sp_weight_violation_scan : user-defined + named exception handling
-- sp_parcel_cost_audit  : %TYPE, %ROWTYPE, arithmetic, comparison operators
@&base.\05-plsql\00-logging-table.sql
@&base.\05-plsql\01-block-structure.sql
@&base.\05-plsql\02-exception-handling.sql
@&base.\05-plsql\03-cursor-intro.sql
