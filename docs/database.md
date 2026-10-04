# Database & Drivers

## Connection

- Engine: MySQL-compatible (MariaDB via XAMPP locally), host `127.0.0.1:3306`.
- Database name: `laravel-boilerplate`.
- Local credentials: user `root`, no password (XAMPP default). Set real credentials via `.env` for any non-local environment.
- Charset: `utf8mb4` / `utf8mb4_unicode_ci`.

To create the database locally (already done for this checkout):

```sql
CREATE DATABASE `laravel-boilerplate` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Requires the PHP `pdo_mysql` (and `mysqli`) extensions enabled in `php.ini`.

## Drivers

Chosen to keep the stack deployable on low-resource shared/cPanel hosting without Redis or a queue worker daemon like Horizon:

| Concern  | Driver     |
|----------|------------|
| Cache    | `database` |
| Queue    | `database` |
| Sessions | `database` |

These all live in the same MySQL database, in the `cache`, `jobs`/`job_batches`/`failed_jobs`, and `sessions` tables created by the default migrations. No Redis, no Memcached, no Horizon.

If a future environment has Redis available and warrants it, switch drivers via `.env` only — don't hardcode a driver choice in config files.

## Migrations touched for UUID keys

- `0001_01_01_000000_create_users_table.php` — `users.id` is `uuid()->primary()`; `sessions.user_id` is `foreignUuid('user_id')`.

See [architecture.md](architecture.md) for the UUIDv7 key convention that drives this.
