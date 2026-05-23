# Setup Notes — Troubleshooting Reference

## OCI8 Extension
- **DLL loaded:** `php_oci8_19.dll` (PECL OCI8 3.x, PHP 8.2 TS x64)
- **Location:** `C:\xampp\php\ext\php_oci8_19.dll`
- **php.ini entry:** `extension=oci8_19`

## Oracle Home
- **Oracle XE path:** `C:\oraclexe\app\oracle\product\11.2.0\server`
- **sqlplus:** `C:\oraclexe\app\oracle\product\11.2.0\server\bin\sqlplus.exe`
- **`ORACLE_HOME` env var:** Not set (Oracle bin is in System PATH)

## Instant Client 23.0
- **Location:** `C:\instantclient_23_0`
- **Added to System PATH:** Yes (at bottom — overridden by DLL copy approach)
- **DLLs copied to:** `C:\xampp\php\` and `C:\xampp\apache\bin\`
- **Why needed:** `php_oci8_19.dll` calls `OCIStmtGetNextResult` (introduced in Oracle 12c).
  Oracle 11g XE's `oci.dll` does not export this function. Instant Client 23.0 does.
  DLLs were copied directly into PHP/Apache directories so they take precedence over
  the Oracle XE `oci.dll` in PATH regardless of PATH order.

## .env Database Connection
```
DB_CONNECTION=oracle
DB_HOST=localhost
DB_PORT=1521
DB_DATABASE=XE
DB_SERVICE_NAME=XE
DB_USERNAME=system
DB_PASSWORD=***masked***
```

## Oracle Identifier Length (30-char limit on Oracle 11g)

Laravel auto-generates index/constraint names as `{table}_{column}_{type}`.
All Breeze migration names fell within the limit — **no renames were needed**.

Names verified (longest ones):
| Generated name                  | Length | Status |
|---------------------------------|--------|--------|
| `sessions_last_activity_index`  | 28     | ✓ OK   |
| `cache_locks_expiration_index`  | 28     | ✓ OK   |
| `users_email_unique`            | 18     | ✓ OK   |
| `failed_jobs_uuid_unique`       | 22     | ✓ OK   |
| `sessions_user_id_index`        | 21     | ✓ OK   |

**Rule for future migrations:** keep `{table}_{column}_{type}` ≤ 30 chars.
If a name would exceed 30 chars, pass an explicit name:
```php
$table->string('email')->unique('short_name_uq');
$table->index(['col'], 'short_idx');
```

## Day 2 Extra Gotcha — ORA-01950 (tablespace privilege)

When `cdb_admin` ran `php artisan migrate`, Oracle returned:
> ORA-01950: no privileges on tablespace 'SYSTEM'

**Cause:** New Oracle users default to the SYSTEM tablespace. `cdb_admin` was given a quota on USERS but not told to use it as default.

**Fix applied:**
```sql
ALTER USER cdb_admin DEFAULT TABLESPACE USERS;
```
Added to `01-create-users.sql` so it is re-runnable. Always set DEFAULT TABLESPACE explicitly when creating schema-owner users.

## Known Gotchas
1. `ORACLE_HOME` not set as a System env var — not required as long as Oracle bin is in PATH.
2. PATH order matters: Instant Client must appear before Oracle XE bin, or use the DLL-copy approach.
3. XAMPP Control Panel must be fully restarted (not just Apache) for PATH changes to propagate to Apache.
4. Laravel 12 was installed instead of 13 (PHP 8.2 does not meet Laravel 13's ^8.3 requirement).
5. yajra/laravel-oci8 v12.11.0 installed (v13 also requires PHP ^8.3).
