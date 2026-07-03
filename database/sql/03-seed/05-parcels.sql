

-- ── BOOKED (5) — no assigned rider yet ────────────────────────────────────
INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600001',1000,1000,1000,1002,NULL,1.5 ,'BOOKED',SYSDATE-2,NULL);

INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600002',1001,1001,1001,1003,NULL,3.0 ,'BOOKED',SYSDATE-1,NULL);

INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600003',1002,1002,1002,1000,NULL,0.5 ,'BOOKED',SYSDATE-3,NULL);

INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600004',1003,1003,1003,1005,NULL,5.0 ,'BOOKED',SYSDATE-1,NULL);

INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600005',1004,1004,1004,1001,NULL,2.0 ,'BOOKED',SYSDATE-2,NULL);

-- ── IN_TRANSIT (8) ────────────────────────────────────────────────────────
INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600006',1005,1005,1000,1003,1000,4.5 ,'IN_TRANSIT',SYSDATE-7,NULL);

INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600007',1006,1006,1001,1004,1002,1.0 ,'IN_TRANSIT',SYSDATE-5,NULL);

INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600008',1007,1007,1002,1000,1004,7.0 ,'IN_TRANSIT',SYSDATE-8,NULL);

INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600009',1008,1008,1003,1001,1006,3.5 ,'IN_TRANSIT',SYSDATE-6,NULL);

INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600010',1009,1009,1004,1002,1008,10.0,'IN_TRANSIT',SYSDATE-9,NULL);

INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600011',1010,1010,1005,1000,1010,2.5 ,'IN_TRANSIT',SYSDATE-4,NULL);

INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600012',1011,1011,1000,1005,1001,6.0 ,'IN_TRANSIT',SYSDATE-7,NULL);

INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600013',1012,1012,1001,1002,1003,1.5 ,'IN_TRANSIT',SYSDATE-5,NULL);

-- ── OUT_FOR_DELIVERY (4) ──────────────────────────────────────────────────
INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600014',1013,1013,1000,1004,1009,8.0 ,'OUT_FOR_DELIVERY',SYSDATE-5,NULL);

INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600015',1014,1014,1002,1001,1002,2.0 ,'OUT_FOR_DELIVERY',SYSDATE-4,NULL);

INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600016',1000,1015,1003,1000,1007,4.0 ,'OUT_FOR_DELIVERY',SYSDATE-6,NULL);

INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600017',1001,1016,1005,1003,1011,15.0,'OUT_FOR_DELIVERY',SYSDATE-3,NULL);

-- ── DELIVERED (10) — delivered_at populated ──────────────────────────────
INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600018',1002,1017,1000,1002,1005,3.0 ,'DELIVERED',SYSDATE-20,SYSDATE-15);

INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600019',1003,1018,1001,1003,1002,1.5 ,'DELIVERED',SYSDATE-18,SYSDATE-14);

INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600020',1004,1019,1002,1004,1004,5.0 ,'DELIVERED',SYSDATE-15,SYSDATE-11);

INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600021',1005,1000,1003,1005,1006,2.5 ,'DELIVERED',SYSDATE-12,SYSDATE-9);

INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600022',1006,1001,1004,1000,1009,12.0,'DELIVERED',SYSDATE-14,SYSDATE-10);

INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600023',1007,1002,1005,1001,1011,4.5 ,'DELIVERED',SYSDATE-10,SYSDATE-7);

INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600024',1008,1003,1000,1003,1001,1.0 ,'DELIVERED',SYSDATE-9,SYSDATE-6);

INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600025',1009,1004,1001,1004,1003,7.5 ,'DELIVERED',SYSDATE-11,SYSDATE-8);

INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600026',1010,1005,1002,1005,1005,3.5 ,'DELIVERED',SYSDATE-8,SYSDATE-5);

INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600027',1011,1006,1003,1001,1007,20.0,'DELIVERED',SYSDATE-7,SYSDATE-4);

-- ── RETURNED (3) ──────────────────────────────────────────────────────────
INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600028',1012,1007,1000,1002,1000,6.0 ,'RETURNED',SYSDATE-12,NULL);

INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600029',1013,1008,1004,1000,1008,2.0 ,'RETURNED',SYSDATE-10,NULL);

INSERT INTO parcels (parcel_id, tracking_code, sender_customer_id, receiver_id,
    origin_branch_id, destination_branch_id, assigned_rider_id,
    weight_kg, current_status, booked_at, delivered_at)
VALUES (seq_parcel_id.NEXTVAL,'CDB202600030',1014,1009,1005,1003,1010,25.0,'RETURNED',SYSDATE-8,NULL);




