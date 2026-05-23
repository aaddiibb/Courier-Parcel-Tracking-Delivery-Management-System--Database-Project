# Schema Design — Courier Parcel Tracking System

## Overview

The schema supports a courier company that books parcels from customers,
routes them through branch offices, and delivers them via riders.
All 8 business tables live in the `cdb_admin` schema.

---

## ER Diagram (ASCII)

```
 CUSTOMERS ──────────────────────────────────────────────────────────┐
    │ customer_id (PK)                                               │
    │                                                                │
    │ 1                                                              │
    ├─────────────────────────────┐                                  │
    │                             │                                  │
    ▼ M                           ▼ M                                │
 PARCELS                       RECEIVERS                             │
    │ parcel_id (PK)               receiver_id (PK)                  │
    │ sender_customer_id (FK)      booking_customer_id (FK) ─────────┘
    │ receiver_id (FK)
    │ origin_branch_id (FK) ──────────────────────┐
    │ destination_branch_id (FK) ─────────────────┤
    │ assigned_rider_id (FK) ──────────────────┐  │
    │                                          │  │
    │ 1                                        │  ▼
    ├─────────────────┐          RIDERS ◄──────┘ BRANCHES
    │                 │            │ rider_id (PK)   branch_id (PK)
    ▼                 ▼            └─► assigned_branch_id (FK)
 PARCEL_STATUS_   DELIVERY_ATTEMPTS
 HISTORY            attempt_id (PK)
    history_id (PK) parcel_id (FK) → PARCELS
    parcel_id (FK)  rider_id (FK)  → RIDERS
    [CASCADE DELETE]
    │
    └──── FEES
            fee_id (PK)
            parcel_id (FK, UNIQUE) → PARCELS
```

---

## Tables

### customers
| Column       | Type          | Constraint        | Default  |
|--------------|---------------|-------------------|----------|
| customer_id  | NUMBER        | PK                |          |
| full_name    | VARCHAR2(100) | NOT NULL          |          |
| phone        | VARCHAR2(20)  | NOT NULL          |          |
| email        | VARCHAR2(100) |                   |          |
| address      | VARCHAR2(300) |                   |          |
| created_at   | DATE          |                   | SYSDATE  |

Sequence: `seq_customer_id`

---

### receivers
| Column               | Type          | Constraint | Default |
|----------------------|---------------|------------|---------|
| receiver_id          | NUMBER        | PK         |         |
| full_name            | VARCHAR2(100) | NOT NULL   |         |
| phone                | VARCHAR2(20)  | NOT NULL   |         |
| address              | VARCHAR2(300) |            |         |
| booking_customer_id  | NUMBER        | FK → customers(customer_id) | |

Sequence: `seq_receiver_id`

---

### branches
| Column       | Type          | Constraint        | Default |
|--------------|---------------|-------------------|---------|
| branch_id    | NUMBER        | PK                |         |
| branch_name  | VARCHAR2(100) | NOT NULL, UNIQUE  |         |
| city         | VARCHAR2(50)  | NOT NULL          |         |
| address      | VARCHAR2(200) |                   |         |
| phone        | VARCHAR2(20)  |                   |         |
| manager_name | VARCHAR2(100) |                   |         |

Sequence: `seq_branch_id`

---

### riders
| Column             | Type         | Constraint                     | Default |
|--------------------|--------------|--------------------------------|---------|
| rider_id           | NUMBER       | PK                             |         |
| full_name          | VARCHAR2(100)| NOT NULL                       |         |
| phone              | VARCHAR2(20) | NOT NULL, UNIQUE               |         |
| vehicle_type       | VARCHAR2(30) |                                |         |
| assigned_branch_id | NUMBER       | FK → branches(branch_id)       |         |
| active_flag        | CHAR(1)      |                                | 'Y'     |

Sequence: `seq_rider_id`

---

### parcels
| Column                | Type         | Constraint                       | Default   |
|-----------------------|--------------|----------------------------------|-----------|
| parcel_id             | NUMBER       | PK                               |           |
| tracking_code         | VARCHAR2(20) | NOT NULL, UNIQUE                 |           |
| sender_customer_id    | NUMBER       | FK → customers(customer_id)      |           |
| receiver_id           | NUMBER       | FK → receivers(receiver_id)      |           |
| origin_branch_id      | NUMBER       | FK → branches(branch_id)         |           |
| destination_branch_id | NUMBER       | FK → branches(branch_id)         |           |
| assigned_rider_id     | NUMBER       | FK → riders(rider_id), nullable  |           |
| weight_kg             | NUMBER(6,2)  |                                  |           |
| current_status        | VARCHAR2(20) |                                  | 'BOOKED'  |
| booked_at             | DATE         |                                  | SYSDATE   |
| delivered_at          | DATE         |                                  |           |

Sequence: `seq_parcel_id`

---

### parcel_status_history
| Column     | Type         | Constraint                              | Default |
|------------|--------------|-----------------------------------------|---------|
| history_id | NUMBER       | PK                                      |         |
| parcel_id  | NUMBER       | FK → parcels(parcel_id) ON DELETE CASCADE|        |
| status     | VARCHAR2(20) |                                         |         |
| changed_at | DATE         |                                         | SYSDATE |
| changed_by | VARCHAR2(50) |                                         |         |
| remarks    | VARCHAR2(200)|                                         |         |

Sequence: `seq_history_id`
> Day 11 trigger will auto-insert rows here on every `parcels.current_status` update.

---

### delivery_attempts
| Column         | Type         | Constraint                  | Default |
|----------------|--------------|-----------------------------|---------|
| attempt_id     | NUMBER       | PK                          |         |
| parcel_id      | NUMBER       | FK → parcels(parcel_id)     |         |
| rider_id       | NUMBER       | FK → riders(rider_id)       |         |
| attempted_at   | DATE         |                             | SYSDATE |
| success_flag   | CHAR(1)      |                             |         |
| failure_reason | VARCHAR2(100)|                             |         |

Sequence: `seq_attempt_id`

---

### fees
| Column        | Type        | Constraint                         | Default |
|---------------|-------------|------------------------------------|---------|
| fee_id        | NUMBER      | PK                                 |         |
| parcel_id     | NUMBER      | FK → parcels(parcel_id), UNIQUE    |         |
| base_amount   | NUMBER(8,2) |                                    |         |
| weight_charge | NUMBER(8,2) |                                    |         |
| total_amount  | NUMBER(8,2) |                                    |         |
| paid_flag     | CHAR(1)     |                                    | 'N'     |
| paid_at       | DATE        |                                    |         |

Sequence: `seq_fee_id`

---

## Relationships

| FK Constraint             | Child Table            | Column                | Parent Table | Parent Column |
|---------------------------|------------------------|-----------------------|--------------|---------------|
| receivers_customer_fk     | receivers              | booking_customer_id   | customers    | customer_id   |
| riders_branch_fk          | riders                 | assigned_branch_id    | branches     | branch_id     |
| parcels_sender_fk         | parcels                | sender_customer_id    | customers    | customer_id   |
| parcels_receiver_fk       | parcels                | receiver_id           | receivers    | receiver_id   |
| parcels_origin_fk         | parcels                | origin_branch_id      | branches     | branch_id     |
| parcels_dest_fk           | parcels                | destination_branch_id | branches     | branch_id     |
| parcels_rider_fk          | parcels                | assigned_rider_id     | riders       | rider_id      |
| psh_parcel_fk             | parcel_status_history  | parcel_id             | parcels      | parcel_id     |
| da_parcel_fk              | delivery_attempts      | parcel_id             | parcels      | parcel_id     |
| da_rider_fk               | delivery_attempts      | rider_id              | riders       | rider_id      |
| fees_parcel_fk            | fees                   | parcel_id             | parcels      | parcel_id     |

Notes:
- `psh_parcel_fk` has `ON DELETE CASCADE` — deleting a parcel removes its entire status history.
- `fees_parcel_uq` enforces exactly one fee record per parcel.
- `parcels.assigned_rider_id` is nullable — parcels start unassigned.
