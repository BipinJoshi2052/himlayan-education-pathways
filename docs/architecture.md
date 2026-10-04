# Architecture

Core conventions that apply across the whole project. Every new module should follow these unless there's a documented reason not to.

## Primary keys: UUIDv7

Every model uses a UUIDv7 primary key — never auto-incrementing integers. This is enforced via a dedicated trait rather than scattering `HasUuids` across every model directly, so the key strategy can be changed project-wide from one place.

- Trait: `App\Common\Traits\HasUuidPrimaryKey` ([app/Common/Traits/HasUuidPrimaryKey.php](../app/Common/Traits/HasUuidPrimaryKey.php))
- Internally wraps Laravel's native `Illuminate\Database\Eloquent\Concerns\HasUuids`, overriding `newUniqueId()` to call `str()->uuid7()` explicitly, and sets `$incrementing = false` / `$keyType = 'string'` via an `initializeHasUuidPrimaryKey()` hook (Eloquent calls `initialize{Trait}` automatically for every trait on a model). Those two properties are set at runtime rather than declared directly on the trait, because declaring them as trait properties with different defaults than `Illuminate\Database\Eloquent\Model` causes a fatal "incompatible property" error — PHP doesn't allow a trait to redeclare a property the base class already declares with a different default.
- UUIDv7 (not v4) is used deliberately: it's timestamp-prefixed, so keys generated close together sort close together, avoiding the random-insert index fragmentation of UUIDv4.
- Migrations: use `$table->uuid('id')->primary()` for the owning table and `$table->foreignUuid('column')` for references to it.
- Applied to: `App\Models\User` (as of 2026-10-02).

**When adding a new model:** `use HasUuidPrimaryKey;` in the model, `$table->uuid('id')->primary()` in its migration, `$table->foreignUuid(...)` on any table referencing it.

### Polymorphic (morph) tables must use UUID-typed columns too

Any package or table that stores a polymorphic `model_type` / `model_id` pair needs the id column typed as `uuid`, not `unsignedBigInteger` — otherwise inserting our string UUIDs into it breaks. Two fixes already applied for this:

- `spatie/laravel-permission`: `config/permission.php` → `column_names.model_morph_key` set to `model_uuid`, **and** the published migration's `model_has_permissions`/`model_has_roles` tables changed from `$table->unsignedBigInteger($columnNames['model_morph_key'])` to `$table->uuid(...)` — renaming the config key alone is not sufficient, the package's stub migration still hardcodes the unsigned-integer column type.
- `spatie/laravel-medialibrary`: published `create_media_table` migration changed from `$table->morphs('model')` to `$table->uuidMorphs('model')`.

Any future polymorphic table (ours or a new package's) needs the same check.

## Action / DTO pattern

No generic Repository layer. Business logic lives in single-purpose **Actions**; data crossing a boundary (request → action, action → view) is carried by typed, immutable **DTOs**. Controllers stay thin: validate (via a Form Request), build a DTO, call an Action, respond.

- Actions: `app/Actions/<Domain>/<VerbNoun>Action.php` — one public `handle()` method per action.
- DTOs: `app/DTOs/<Domain>/<Name>Data.php` — `final readonly class`, constructor-promoted properties, a `fromArray()` named constructor where useful.
- Form Requests: `app/Http/Requests/<Domain>/<Name>Request.php` — validation only, controller still builds the DTO.

**Reference implementations:**
- Login flow (simplest case — one DTO, one action): [app/DTOs/Auth/LoginData.php](../app/DTOs/Auth/LoginData.php), [app/Actions/Auth/AttemptLoginAction.php](../app/Actions/Auth/AttemptLoginAction.php), [app/Http/Requests/Auth/LoginRequest.php](../app/Http/Requests/Auth/LoginRequest.php), [app/Http/Controllers/Auth/LoginController.php](../app/Http/Controllers/Auth/LoginController.php)
- Full CRUD with a nested child resource (richer case — one action handles both create and update, syncing a repeater of child rows): see [cms-sections.md](cms-sections.md)'s `App\Actions\Cms\SaveSectionAction`

## Strict models

`Model::shouldBeStrict()` is enabled outside production (`App\Providers\AppServiceProvider::boot()`), so in local/staging:

- Accessing a non-existent attribute throws instead of silently returning `null`.
- Lazy-loading a relationship that wasn't eager-loaded throws.
- Filling a guarded/non-fillable attribute throws.

This surfaces mistakes during development that would otherwise fail silently or only show up as subtle bugs in production.

## Frontend (public-facing)

Plain HTML5 + CSS + vanilla JavaScript/jQuery, server-rendered via Blade. No SPA framework (no Vue/React) for the public site. Assets live under `public/css` and `public/js` and are referenced with `asset()` — no build step (no Vite/webpack).

## Admin UI

The admin area (and the sign-in page) uses the Hope UI Bootstrap 5 theme, integrated as static CSS/JS under `public/admin-assets/` and rendered through Blade — same no-build-step philosophy as the public site, just a different (purchased/templated) visual design. Full details, including a routing collision that must not be reintroduced, are in **[admin-ui.md](admin-ui.md)**.

### `/admin-design-template` (local reference only, git-ignored)

The source Hope UI theme files, dropped into the project root as the visual reference the admin UI is built from. It's `git-ignored` (not part of the repo) — only the specific files actually needed were copied into `public/admin-assets/`, which *is* tracked and ships with the app. See [admin-ui.md](admin-ui.md) for exactly what was copied and what wasn't.
