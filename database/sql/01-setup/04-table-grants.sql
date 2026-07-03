
GRANT SELECT, INSERT, UPDATE ON cdb_admin.parcels               TO role_branch_mgr;
GRANT SELECT, INSERT, UPDATE ON cdb_admin.parcel_status_history TO role_branch_mgr;
GRANT SELECT, INSERT, UPDATE ON cdb_admin.delivery_attempts     TO role_branch_mgr;
GRANT SELECT, INSERT, UPDATE ON cdb_admin.fees                  TO role_branch_mgr;

-- role_rider
GRANT SELECT ON cdb_admin.parcels TO role_rider;
GRANT SELECT ON cdb_admin.riders  TO role_rider;
GRANT UPDATE ON cdb_admin.parcels TO role_rider;

-- role_customer
GRANT SELECT ON cdb_admin.parcels               TO role_customer;
GRANT SELECT ON cdb_admin.parcel_status_history TO role_customer;

PROMPT Table-level grants applied.
