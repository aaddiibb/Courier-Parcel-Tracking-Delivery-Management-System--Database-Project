-- 01-customers.sql
-- 15 customers across Dhaka, Chittagong, Sylhet, Khulna, Rajshahi.
-- Run as: cdb_admin@XE  Sequences start at 1000 on first run.

INSERT INTO customers (customer_id, full_name, phone, email, address, created_at)
VALUES (seq_customer_id.NEXTVAL, 'Karim Uddin Ahmed',   '01711-100001', 'karim.ahmed@gmail.com',   '14 Mirpur Road, Dhaka',           SYSDATE - 90);

INSERT INTO customers (customer_id, full_name, phone, email, address, created_at)
VALUES (seq_customer_id.NEXTVAL, 'Nasrin Sultana',      '01711-100002', 'nasrin.sultana@yahoo.com','28 Banani Ave, Dhaka',            SYSDATE - 85);

INSERT INTO customers (customer_id, full_name, phone, email, address, created_at)
VALUES (seq_customer_id.NEXTVAL, 'Rafiqul Islam',       '01811-200001', 'rafiq.islam@gmail.com',   '5 Agrabad, Chittagong',           SYSDATE - 80);

INSERT INTO customers (customer_id, full_name, phone, email, address, created_at)
VALUES (seq_customer_id.NEXTVAL, 'Fariha Begum',        '01611-300001', 'fariha.begum@gmail.com',  '12 Zindabazar, Sylhet',           SYSDATE - 75);

INSERT INTO customers (customer_id, full_name, phone, email, address, created_at)
VALUES (seq_customer_id.NEXTVAL, 'Shahadat Hossain',    '01911-400001', 'shahadat.h@outlook.com',  '3 KDA Avenue, Khulna',            SYSDATE - 70);

INSERT INTO customers (customer_id, full_name, phone, email, address, created_at)
VALUES (seq_customer_id.NEXTVAL, 'Morsheda Khatun',     '01711-100010', 'morsheda.k@gmail.com',    '8 New Market, Dhaka',             SYSDATE - 65);

INSERT INTO customers (customer_id, full_name, phone, email, address, created_at)
VALUES (seq_customer_id.NEXTVAL, 'Jahirul Alam',        '01811-200010', 'jahir.alam@gmail.com',    '17 Patenga, Chittagong',          SYSDATE - 60);

INSERT INTO customers (customer_id, full_name, phone, email, address, created_at)
VALUES (seq_customer_id.NEXTVAL, 'Taslima Akter',       '01611-300010', 'taslima.a@hotmail.com',   '6 Ambarkhana, Sylhet',            SYSDATE - 55);

INSERT INTO customers (customer_id, full_name, phone, email, address, created_at)
VALUES (seq_customer_id.NEXTVAL, 'Minhajul Abedin',     '01911-400010', 'minhaj.ab@gmail.com',     '22 Sonadanga, Khulna',            SYSDATE - 50);

INSERT INTO customers (customer_id, full_name, phone, email, address, created_at)
VALUES (seq_customer_id.NEXTVAL, 'Sadia Rahman',        '01511-500001', 'sadia.rahman@gmail.com',  '9 Shaheb Bazar, Rajshahi',        SYSDATE - 45);

INSERT INTO customers (customer_id, full_name, phone, email, address, created_at)
VALUES (seq_customer_id.NEXTVAL, 'Belal Hossain',       '01711-100020', 'belal.h@gmail.com',       '31 Dhanmondi, Dhaka',             SYSDATE - 40);

INSERT INTO customers (customer_id, full_name, phone, email, address, created_at)
VALUES (seq_customer_id.NEXTVAL, 'Rowshan Ara',         '01811-200020', 'rowshan.ara@gmail.com',   '44 Halishahar, Chittagong',       SYSDATE - 35);

INSERT INTO customers (customer_id, full_name, phone, email, address, created_at)
VALUES (seq_customer_id.NEXTVAL, 'Touhidul Islam',      '01611-300020', 'touhid.i@gmail.com',      '2 Tilagarh, Sylhet',              SYSDATE - 30);

INSERT INTO customers (customer_id, full_name, phone, email, address, created_at)
VALUES (seq_customer_id.NEXTVAL, 'Mahmuda Parvin',      '01511-500010', 'mahmuda.p@yahoo.com',     '15 Uposhahar, Rajshahi',          SYSDATE - 25);

INSERT INTO customers (customer_id, full_name, phone, email, address, created_at)
VALUES (seq_customer_id.NEXTVAL, 'Zahirul Haque',       '01911-400020', 'zahir.haque@gmail.com',   '7 Boyra, Khulna',                 SYSDATE - 20);

PROMPT 15 customers inserted.
