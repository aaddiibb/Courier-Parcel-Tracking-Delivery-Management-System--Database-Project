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
