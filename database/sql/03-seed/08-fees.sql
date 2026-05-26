-- 08-fees.sql
-- One fee row per parcel. base_amount=50, weight_charge=weight_kg*20.
-- DELIVERED parcels (1017–1026): paid_flag='Y', paid_at=delivered_at+1.
-- All others: paid_flag='N', paid_at=NULL.
-- Run as: cdb_admin@XE

-- ── BOOKED parcels ────────────────────────────────────────────────────────
-- P1000: 1.5kg → 50 + 30 = 80
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1000,50,30,80,'N',NULL);
-- P1001: 3.0kg → 50 + 60 = 110
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1001,50,60,110,'N',NULL);
-- P1002: 0.5kg → 50 + 10 = 60
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1002,50,10,60,'N',NULL);
-- P1003: 5.0kg → 50 + 100 = 150
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1003,50,100,150,'N',NULL);
-- P1004: 2.0kg → 50 + 40 = 90
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1004,50,40,90,'N',NULL);

-- ── IN_TRANSIT parcels ────────────────────────────────────────────────────
-- P1005: 4.5kg → 140
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1005,50,90,140,'N',NULL);
-- P1006: 1.0kg → 70
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1006,50,20,70,'N',NULL);
-- P1007: 7.0kg → 190
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1007,50,140,190,'N',NULL);
-- P1008: 3.5kg → 120
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1008,50,70,120,'N',NULL);
-- P1009: 10.0kg → 250
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1009,50,200,250,'N',NULL);
-- P1010: 2.5kg → 100
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1010,50,50,100,'N',NULL);
-- P1011: 6.0kg → 170
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1011,50,120,170,'N',NULL);
-- P1012: 1.5kg → 80
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1012,50,30,80,'N',NULL);

-- ── OUT_FOR_DELIVERY parcels ──────────────────────────────────────────────
-- P1013: 8.0kg → 210
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1013,50,160,210,'N',NULL);
-- P1014: 2.0kg → 90
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1014,50,40,90,'N',NULL);
-- P1015: 4.0kg → 130
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1015,50,80,130,'N',NULL);
-- P1016: 15.0kg → 350
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1016,50,300,350,'N',NULL);

-- ── DELIVERED parcels — paid ──────────────────────────────────────────────
-- P1017: 3.0kg → 110
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1017,50,60,110,'Y',SYSDATE-14);
-- P1018: 1.5kg → 80
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1018,50,30,80,'Y',SYSDATE-13);
-- P1019: 5.0kg → 150
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1019,50,100,150,'Y',SYSDATE-10);
-- P1020: 2.5kg → 100
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1020,50,50,100,'Y',SYSDATE-8);
-- P1021: 12.0kg → 290
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1021,50,240,290,'Y',SYSDATE-9);
-- P1022: 4.5kg → 140
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1022,50,90,140,'Y',SYSDATE-6);
-- P1023: 1.0kg → 70
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1023,50,20,70,'Y',SYSDATE-5);
-- P1024: 7.5kg → 200
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1024,50,150,200,'Y',SYSDATE-7);
-- P1025: 3.5kg → 120
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1025,50,70,120,'Y',SYSDATE-4);
-- P1026: 20.0kg → 450
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1026,50,400,450,'Y',SYSDATE-3);

-- ── RETURNED parcels ──────────────────────────────────────────────────────
-- P1027: 6.0kg → 170
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1027,50,120,170,'N',NULL);
-- P1028: 2.0kg → 90
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1028,50,40,90,'N',NULL);
-- P1029: 25.0kg → 550
INSERT INTO fees (fee_id,parcel_id,base_amount,weight_charge,total_amount,paid_flag,paid_at)
VALUES (seq_fee_id.NEXTVAL,1029,50,500,550,'N',NULL);

COMMIT;

PROMPT 30 fee rows inserted. Transaction committed.
