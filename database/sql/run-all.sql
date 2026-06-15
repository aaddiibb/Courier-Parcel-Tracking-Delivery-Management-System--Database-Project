-- run-all.sql
-- Master script. Run from SQL*Plus from the database/sql/ directory.
-- Day 2 (as SYSTEM): sqlplus system/<pw>@XE @run-all.sql
-- Day 3+ (as cdb_admin): sqlplus cdb_admin/<pw>@XE @run-all.sql

-- Day 2: users, roles, privileges (requires SYSTEM)
@@01-setup/01-create-users.sql
@@01-setup/02-create-roles.sql
@@01-setup/03-grant-privileges.sql

-- Day 3: sequences and business tables (as cdb_admin)
@@02-schema/01-sequences.sql
@@02-schema/02-customers.sql
@@02-schema/03-receivers.sql
@@02-schema/04-branches.sql
@@02-schema/05-riders.sql
@@02-schema/06-parcels.sql
@@02-schema/07-parcel-status-history.sql
@@02-schema/08-delivery-attempts.sql
@@02-schema/09-fees.sql

-- Day 4: constraints, column modifications, seed data, DML demos
@@02-schema/10-alter-constraints.sql
@@02-schema/11-alter-modify.sql
@@03-seed/01-customers.sql
@@03-seed/02-receivers.sql
@@03-seed/03-branches.sql
@@03-seed/04-riders.sql
@@03-seed/05-parcels.sql
@@03-seed/06-parcel-status-history.sql
@@03-seed/07-delivery-attempts.sql
@@03-seed/08-fees.sql
@@03-seed/09-dml-demos.sql

-- Day 9: PL/SQL basics — logging table + anonymous blocks (as cdb_admin)
@@05-plsql/00-logging-table.sql
@@05-plsql/01-block-structure.sql
@@05-plsql/02-exception-handling.sql
@@05-plsql/03-cursor-intro.sql
