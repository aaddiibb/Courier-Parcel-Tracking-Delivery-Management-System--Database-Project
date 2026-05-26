-- 02-receivers.sql
-- 20 receivers linked to customers via booking_customer_id.
-- booking_customer_id references customer_ids 1000–1014.
-- Run as: cdb_admin@XE

-- Receivers 1000–1014: one per customer
INSERT INTO receivers (receiver_id, full_name, phone, address, booking_customer_id)
VALUES (seq_receiver_id.NEXTVAL, 'Anwar Hossain',     '01711-900001', '5 Uttara Sector 4, Dhaka',       1000);

INSERT INTO receivers (receiver_id, full_name, phone, address, booking_customer_id)
VALUES (seq_receiver_id.NEXTVAL, 'Ruma Akter',        '01811-900002', '9 CDA Avenue, Chittagong',        1001);

INSERT INTO receivers (receiver_id, full_name, phone, address, booking_customer_id)
VALUES (seq_receiver_id.NEXTVAL, 'Salim Mia',         '01611-900003', '3 Subhanighat, Sylhet',           1002);

INSERT INTO receivers (receiver_id, full_name, phone, address, booking_customer_id)
VALUES (seq_receiver_id.NEXTVAL, 'Hasina Begum',      '01911-900004', '18 Rupsha, Khulna',               1003);

INSERT INTO receivers (receiver_id, full_name, phone, address, booking_customer_id)
VALUES (seq_receiver_id.NEXTVAL, 'Nurul Amin',        '01511-900005', '6 Padma Residential, Rajshahi',   1004);

INSERT INTO receivers (receiver_id, full_name, phone, address, booking_customer_id)
VALUES (seq_receiver_id.NEXTVAL, 'Dilruba Nahar',     '01711-900006', '12 Rayer Bazar, Dhaka',           1005);

INSERT INTO receivers (receiver_id, full_name, phone, address, booking_customer_id)
VALUES (seq_receiver_id.NEXTVAL, 'Forkan Ali',        '01811-900007', '2 Kotwali, Chittagong',           1006);

INSERT INTO receivers (receiver_id, full_name, phone, address, booking_customer_id)
VALUES (seq_receiver_id.NEXTVAL, 'Shirina Khanam',    '01611-900008', '7 Shahjalal Upashahar, Sylhet',   1007);

INSERT INTO receivers (receiver_id, full_name, phone, address, booking_customer_id)
VALUES (seq_receiver_id.NEXTVAL, 'Rezaul Karim',      '01911-900009', '31 Khalishpur, Khulna',           1008);

INSERT INTO receivers (receiver_id, full_name, phone, address, booking_customer_id)
VALUES (seq_receiver_id.NEXTVAL, 'Nazmun Nahar',      '01511-900010', '4 Boalia, Rajshahi',              1009);

INSERT INTO receivers (receiver_id, full_name, phone, address, booking_customer_id)
VALUES (seq_receiver_id.NEXTVAL, 'Aminul Islam',      '01711-900011', '55 Mohammadpur, Dhaka',           1010);

INSERT INTO receivers (receiver_id, full_name, phone, address, booking_customer_id)
VALUES (seq_receiver_id.NEXTVAL, 'Parveen Akhter',    '01811-900012', '8 Nasirabad, Chittagong',         1011);

INSERT INTO receivers (receiver_id, full_name, phone, address, booking_customer_id)
VALUES (seq_receiver_id.NEXTVAL, 'Kamrul Hasan',      '01611-900013', '14 Mira Bazar, Sylhet',           1012);

INSERT INTO receivers (receiver_id, full_name, phone, address, booking_customer_id)
VALUES (seq_receiver_id.NEXTVAL, 'Sabrina Islam',     '01511-900014', '22 Motihar, Rajshahi',            1013);

INSERT INTO receivers (receiver_id, full_name, phone, address, booking_customer_id)
VALUES (seq_receiver_id.NEXTVAL, 'Iqbal Hossain',     '01911-900015', '10 Daulatpur, Khulna',            1014);

-- Receivers 1015–1019: additional for varied parcel mapping
INSERT INTO receivers (receiver_id, full_name, phone, address, booking_customer_id)
VALUES (seq_receiver_id.NEXTVAL, 'Lutfa Begum',       '01711-900016', '77 Tejgaon, Dhaka',               1000);

INSERT INTO receivers (receiver_id, full_name, phone, address, booking_customer_id)
VALUES (seq_receiver_id.NEXTVAL, 'Shariful Islam',    '01811-900017', '21 Bakalia, Chittagong',          1001);

INSERT INTO receivers (receiver_id, full_name, phone, address, booking_customer_id)
VALUES (seq_receiver_id.NEXTVAL, 'Meherun Nessa',     '01611-900018', '5 Bondor, Sylhet',                1002);

INSERT INTO receivers (receiver_id, full_name, phone, address, booking_customer_id)
VALUES (seq_receiver_id.NEXTVAL, 'Abdur Rahim',       '01911-900019', '16 Gilatala, Khulna',             1003);

INSERT INTO receivers (receiver_id, full_name, phone, address, booking_customer_id)
VALUES (seq_receiver_id.NEXTVAL, 'Kohinoor Akter',    '01511-900020', '33 Rajpara, Rajshahi',            1004);

PROMPT 20 receivers inserted.
