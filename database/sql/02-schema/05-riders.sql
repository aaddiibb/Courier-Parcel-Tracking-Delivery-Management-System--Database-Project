
BEGIN EXECUTE IMMEDIATE 'DROP TABLE riders';
EXCEPTION WHEN OTHERS THEN IF SQLCODE = -942 THEN NULL; ELSE RAISE; END IF; END;
/

CREATE TABLE riders (
    rider_id            NUMBER          CONSTRAINT riders_pk PRIMARY KEY,
    full_name           VARCHAR2(100)   NOT NULL,
    phone               VARCHAR2(20)    NOT NULL CONSTRAINT riders_phone_uq UNIQUE,
    vehicle_type        VARCHAR2(30),
    assigned_branch_id  NUMBER          CONSTRAINT riders_branch_fk
                                            REFERENCES branches(branch_id),
    active_flag         CHAR(1)         DEFAULT 'Y'
);


