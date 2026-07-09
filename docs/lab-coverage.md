# Lab Coverage

| Lab # | Topic                                      | Planned Day | Implementation Location |
|-------|--------------------------------------------|-------------|-------------------------|
| 1     | Environment setup                          | Day 1       |                         |
| 2     | Users & privileges                         | Days 2, 10  | database/sql/01-setup/ (Oracle users/roles/grants); **Day 10 (app layer)**: `EnsureUserHasRole` middleware enforces `admin\|branch_mgr\|rider\|customer` at the HTTP layer, mirroring the DB privilege concept — each role sees only its own routes; `RoleRedirect::path()` is the single source of truth for role→URL mapping |
| 3     | DDL                                        | Days 3–4    | database/sql/02-schema/01–09 (CREATE TABLE/SEQUENCE); 02-schema/10-alter-constraints.sql, 11-alter-modify.sql (ALTER TABLE) |
| 4     | DML                                        | Days 4, 5, 6| database/sql/03-seed/01–08 (INSERT); 03-seed/09-dml-demos.sql (UPDATE × 2, DELETE × 1); **also** app/Http/Controllers/Admin/{Customer,Branch,Rider}Controller.php (store=INSERT, update=UPDATE, destroy=DELETE via raw SQL) |
| 5     | Transactions                               | Day 12      |                         |
| 6     | Constraints                                | Day 4       | database/sql/02-schema/10-alter-constraints.sql (9 named constraints: CHECK × 7, UNIQUE × 2) |
| 7     | Aggregates (COUNT, SUM, AVG, MAX, MIN, conditional SUM(CASE)) | Day 8 | app/Http/Controllers/Lab/AggregateController.php (queries a–f); resources/views/lab/aggregates.blade.php |
| 8     | HAVING (single + multi-condition); subqueries (scalar, IN, EXISTS, NOT EXISTS, inline view, SELECT-list scalar) | Day 8 | app/Http/Controllers/Lab/AggregateController.php (queries g–j); app/Http/Controllers/Lab/SubqueryController.php (queries a–f); resources/views/lab/subqueries.blade.php |
| 9     | Joins                                      | Day 7       | app/Http/Controllers/Lab/JoinController.php (queries a–g: INNER, LEFT, RIGHT, FULL OUTER, CROSS, NATURAL, SELF) |
| 10    | Multi-column + natural join                | Day 7       | app/Http/Controllers/Lab/JoinController.php (query h: multi-column WHERE across joined tables; query f: NATURAL JOIN) |
| 11    | PL/SQL basics (block structure, operators, exception handling, explicit cursor) | Day 9 | database/sql/05-plsql/01-block-structure.sql (Blocks A–C: arithmetic, %TYPE + comparison/logical, %ROWTYPE + :=); 05-plsql/02-exception-handling.sql (NO_DATA_FOUND, TOO_MANY_ROWS, user-defined exception); 05-plsql/03-cursor-intro.sql (explicit cursor); app/Http/Controllers/Lab/PlsqlController.php; resources/views/lab/plsql.blade.php |
| 12    | Control flow + procedures + functions + triggers | Days 10–11 |                    |
