-- 06-parcel-status-history.sql
-- History rows for every parcel leading to its current_status.
-- BOOKED=1 row, IN_TRANSIT=2, OUT_FOR_DELIVERY=3, DELIVERED=4, RETURNED=4.
-- Parcel IDs 1000–1029 match the order inserted in 05-parcels.sql.
-- Run as: cdb_admin@XE

-- ── BOOKED parcels (1000–1004): 1 row each ────────────────────────────────
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1000,'BOOKED',SYSDATE-2,'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1001,'BOOKED',SYSDATE-1,'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1002,'BOOKED',SYSDATE-3,'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1003,'BOOKED',SYSDATE-1,'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1004,'BOOKED',SYSDATE-2,'system','Parcel booked');

-- ── IN_TRANSIT parcels (1005–1012): 2 rows each ───────────────────────────
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1005,'BOOKED',    SYSDATE-7,'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1005,'IN_TRANSIT',SYSDATE-6,'cdb_admin','Picked up from Dhaka Central');

INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1006,'BOOKED',    SYSDATE-5,'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1006,'IN_TRANSIT',SYSDATE-4,'cdb_admin','In transit to Khulna');

INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1007,'BOOKED',    SYSDATE-8,'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1007,'IN_TRANSIT',SYSDATE-7,'cdb_admin','Picked up from Chittagong');

INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1008,'BOOKED',    SYSDATE-6,'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1008,'IN_TRANSIT',SYSDATE-5,'cdb_admin','In transit to Dhaka North');

INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1009,'BOOKED',    SYSDATE-9,'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1009,'IN_TRANSIT',SYSDATE-8,'cdb_admin','In transit to Chittagong');

INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1010,'BOOKED',    SYSDATE-4,'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1010,'IN_TRANSIT',SYSDATE-3,'cdb_admin','In transit to Dhaka Central');

INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1011,'BOOKED',    SYSDATE-7,'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1011,'IN_TRANSIT',SYSDATE-6,'cdb_admin','In transit to Rajshahi');

INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1012,'BOOKED',    SYSDATE-5,'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1012,'IN_TRANSIT',SYSDATE-4,'cdb_admin','In transit to Chittagong');

-- ── OUT_FOR_DELIVERY parcels (1013–1016): 3 rows each ─────────────────────
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1013,'BOOKED',           SYSDATE-5,'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1013,'IN_TRANSIT',       SYSDATE-4,'cdb_admin','In transit to Khulna');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1013,'OUT_FOR_DELIVERY', SYSDATE-1,'cdb_admin','Out for delivery in Khulna');

INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1014,'BOOKED',           SYSDATE-4,'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1014,'IN_TRANSIT',       SYSDATE-3,'cdb_admin','In transit to Dhaka North');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1014,'OUT_FOR_DELIVERY', SYSDATE-1,'cdb_admin','Out for delivery');

INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1015,'BOOKED',           SYSDATE-6,'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1015,'IN_TRANSIT',       SYSDATE-5,'cdb_admin','In transit to Dhaka Central');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1015,'OUT_FOR_DELIVERY', SYSDATE-1,'cdb_admin','Out for delivery in Dhaka');

INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1016,'BOOKED',           SYSDATE-3,'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1016,'IN_TRANSIT',       SYSDATE-2,'cdb_admin','In transit to Sylhet');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1016,'OUT_FOR_DELIVERY', SYSDATE-1,'cdb_admin','Out for delivery in Sylhet');

-- ── DELIVERED parcels (1017–1026): 4 rows each ────────────────────────────
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1017,'BOOKED',           SYSDATE-20,'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1017,'IN_TRANSIT',       SYSDATE-18,'cdb_admin','In transit');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1017,'OUT_FOR_DELIVERY', SYSDATE-16,'cdb_admin','Out for delivery');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1017,'DELIVERED',        SYSDATE-15,'cdb_admin','Delivered successfully');

INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1018,'BOOKED',           SYSDATE-18,'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1018,'IN_TRANSIT',       SYSDATE-17,'cdb_admin','In transit');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1018,'OUT_FOR_DELIVERY', SYSDATE-15,'cdb_admin','Out for delivery');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1018,'DELIVERED',        SYSDATE-14,'cdb_admin','Delivered successfully');

INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1019,'BOOKED',           SYSDATE-15,'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1019,'IN_TRANSIT',       SYSDATE-14,'cdb_admin','In transit');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1019,'OUT_FOR_DELIVERY', SYSDATE-12,'cdb_admin','Out for delivery');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1019,'DELIVERED',        SYSDATE-11,'cdb_admin','Delivered successfully');

INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1020,'BOOKED',           SYSDATE-12,'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1020,'IN_TRANSIT',       SYSDATE-11,'cdb_admin','In transit');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1020,'OUT_FOR_DELIVERY', SYSDATE-10,'cdb_admin','Out for delivery');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1020,'DELIVERED',        SYSDATE-9, 'cdb_admin','Delivered successfully');

INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1021,'BOOKED',           SYSDATE-14,'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1021,'IN_TRANSIT',       SYSDATE-13,'cdb_admin','In transit');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1021,'OUT_FOR_DELIVERY', SYSDATE-11,'cdb_admin','Out for delivery');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1021,'DELIVERED',        SYSDATE-10,'cdb_admin','Delivered successfully');

INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1022,'BOOKED',           SYSDATE-10,'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1022,'IN_TRANSIT',       SYSDATE-9, 'cdb_admin','In transit');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1022,'OUT_FOR_DELIVERY', SYSDATE-8, 'cdb_admin','Out for delivery');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1022,'DELIVERED',        SYSDATE-7, 'cdb_admin','Delivered successfully');

INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1023,'BOOKED',           SYSDATE-9, 'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1023,'IN_TRANSIT',       SYSDATE-8, 'cdb_admin','In transit');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1023,'OUT_FOR_DELIVERY', SYSDATE-7, 'cdb_admin','Out for delivery');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1023,'DELIVERED',        SYSDATE-6, 'cdb_admin','Delivered successfully');

INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1024,'BOOKED',           SYSDATE-11,'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1024,'IN_TRANSIT',       SYSDATE-10,'cdb_admin','In transit');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1024,'OUT_FOR_DELIVERY', SYSDATE-9, 'cdb_admin','Out for delivery');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1024,'DELIVERED',        SYSDATE-8, 'cdb_admin','Delivered successfully');

INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1025,'BOOKED',           SYSDATE-8, 'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1025,'IN_TRANSIT',       SYSDATE-7, 'cdb_admin','In transit');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1025,'OUT_FOR_DELIVERY', SYSDATE-6, 'cdb_admin','Out for delivery');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1025,'DELIVERED',        SYSDATE-5, 'cdb_admin','Delivered successfully');

INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1026,'BOOKED',           SYSDATE-7, 'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1026,'IN_TRANSIT',       SYSDATE-6, 'cdb_admin','In transit');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1026,'OUT_FOR_DELIVERY', SYSDATE-5, 'cdb_admin','Out for delivery');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1026,'DELIVERED',        SYSDATE-4, 'cdb_admin','Delivered successfully');

-- ── RETURNED parcels (1027–1029): 4 rows each ─────────────────────────────
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1027,'BOOKED',           SYSDATE-12,'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1027,'IN_TRANSIT',       SYSDATE-11,'cdb_admin','In transit');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1027,'OUT_FOR_DELIVERY', SYSDATE-9, 'cdb_admin','Out for delivery attempt');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1027,'RETURNED',         SYSDATE-7, 'cdb_admin','Receiver unavailable — returned');

INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1028,'BOOKED',           SYSDATE-10,'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1028,'IN_TRANSIT',       SYSDATE-9, 'cdb_admin','In transit');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1028,'OUT_FOR_DELIVERY', SYSDATE-7, 'cdb_admin','Out for delivery attempt');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1028,'RETURNED',         SYSDATE-5, 'cdb_admin','Wrong address — returned');

INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1029,'BOOKED',           SYSDATE-8, 'system','Parcel booked');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1029,'IN_TRANSIT',       SYSDATE-7, 'cdb_admin','In transit');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1029,'OUT_FOR_DELIVERY', SYSDATE-5, 'cdb_admin','Out for delivery attempt');
INSERT INTO parcel_status_history (history_id,parcel_id,status,changed_at,changed_by,remarks)
VALUES (seq_history_id.NEXTVAL,1029,'RETURNED',         SYSDATE-3, 'cdb_admin','Parcel refused by receiver');

PROMPT 85 status history rows inserted.
