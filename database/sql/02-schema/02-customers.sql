

BEGIN EXECUTE IMMEDIATE 'DROP TABLE customers';
EXCEPTION WHEN OTHERS THEN IF SQLCODE = -942 THEN NULL; ELSE RAISE; END IF; END;
/

CREATE TABLE customers (
    customer_id  NUMBER          CONSTRAINT customers_pk PRIMARY KEY,
    full_name    VARCHAR2(100)   NOT NULL,
    phone        VARCHAR2(20)    NOT NULL,
    email        VARCHAR2(100),
    address      VARCHAR2(300),
    created_at   DATE            DEFAULT SYSDATE
);


