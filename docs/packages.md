# Packages

Keep this file in sync with `composer.json` whenever a package is added or removed. State why a package was chosen, not just that it's installed.

## Installed

| Package | Version constraint | Purpose |
|---|---|---|
| `laravel/framework` | `^13.17` | Core framework. |
| `laravel/tinker` | `^3.0` | REPL for debugging/seeding via `php artisan tinker`. |
| `spatie/laravel-permission` | `^8.3` | Roles & permissions. Configured for UUID morphs: `config/permission.php` → `column_names.model_morph_key` = `model_uuid`, and the published migration's morph columns changed to `$table->uuid(...)` (the config rename alone doesn't change the stub's column type — see [architecture.md](architecture.md)). `User` uses `HasRoles` (as of 2026-10-02). `Role`/`Permission` themselves keep the package's default auto-increment integer ID — they're internal taxonomy tables, not UUID-keyed domain models, so this is a deliberate exception to the UUID-everywhere rule. |
| `spatie/laravel-translatable` | `^6.14` | JSON-column translations for multi-language model fields. In use on `Section`/`SectionItem` (`title`/`subtitle`/`content` and `title`/`description` respectively) — see [cms-sections.md](cms-sections.md). Admin UI currently only edits the app's current locale; the schema is ready for more. |
| `spatie/laravel-medialibrary` | `^11.23` | File/media attachments. Published `create_media_table` migration changed from `$table->morphs('model')` to `$table->uuidMorphs('model')` to match our UUID keys. Requires PHP's `exif` extension (enabled in `php.ini` for this). `Section`/`SectionItem` implement `HasMedia`/`InteractsWithMedia`, but no admin upload UI exists yet — the capability is wired, nothing uses it. |
| `spatie/laravel-query-builder` | `^7.3` | Filtering/sorting via query params. First real use: `GET /admin/sections?filter[page_slug]=home` (see [cms-sections.md](cms-sections.md)). Note: `allowedFilters()`/`allowedSorts()` are variadic (`...$args`), not array-accepting — passing an array throws a runtime `TypeError`. |

### Dev-only

| Package | Purpose |
|---|---|
| `fakerphp/faker` | Fake data for factories/seeders. |
| `laravel/pail` | Tail application logs from the CLI. |
| `laravel/pint` | Opinionated PHP code style fixer (run before committing). |
| `mockery/mockery` | Test doubles. |
| `nunomaduro/collision` | Readable CLI error output for tests/artisan. |
| `phpunit/phpunit` | Test runner. |

## Explicitly not used

- **No Vue/React/SPA framework.** Public frontend is server-rendered Blade + vanilla HTML/CSS/jQuery (see [architecture.md](architecture.md)).
- **No Redis / Horizon.** Cache, queue, and session all use the `database` driver (see [database.md](database.md)).
- **No generic Repository package/pattern.** Business logic lives in Actions (see [architecture.md](architecture.md)).
