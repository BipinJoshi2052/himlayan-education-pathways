# Settings & SEO Resolution

## Current state (as of 2026-10-02)

A key-value `settings` table backs a tabbed "Settings" admin page (General, SEO & Social, SMTP/Mail, Appearance), plus a public-facing SEO fallback resolver.

### Schema & model

`settings`: `key` (string, **primary key** — not UUID, see below), `group` (indexed), `value` (JSON, nullable), timestamps. `App\Models\Setting`:

- `Setting::get(string $key, $default = null)` — cached lookup.
- `Setting::set(string $key, $value, string $group = 'general')` — upserts and invalidates the cache.
- `Setting::getTranslatable(string $key, ?string $locale = null, $default = null)` — for a setting whose `value` is a locale-keyed array (e.g. `['en' => '...', 'ne' => '...']`), resolves current locale → app fallback locale → `$default`.

**Deliberate exception to "every model uses a UUID primary key"** ([architecture.md](architecture.md)): `Setting`'s primary key is the key string itself (`'seo_meta_title'`, `'site_name'`, ...) — its natural identity, same category of documented exception already made for `Role`/`Permission`.

### A real bug, found and fixed via actual testing: don't cache raw Eloquent collections on the `database` cache driver

The first implementation cached `static::all()->keyBy('key')` — a `Collection` of `Setting` model instances — with `Cache::rememberForever()`. Every read crashed with a PHP-level `"incomplete object"` (later `"Cannot use object of type __PHP_Incomplete_Class as array"`) error on `unserialize()`. This was reproduced in isolation (caching a `User` collection the same way failed identically) and is **not** specific to this model — it's the `database` cache driver's serialize/unserialize round-trip breaking on Eloquent model objects in this environment.

The fix, and the reason it's safe to trust: `spatie/laravel-permission`'s `PermissionRegistrar` caches through the exact same `database` driver and had been working all session — its `getSerializedPermissionsForCache(): array` explicitly flattens to a plain array *before* caching, never caching raw models. `Setting::get()` now does the same: `static::query()->pluck('value', 'key')->all()` — a plain PHP array, no model objects — caches and reads back cleanly.

**Takeaway for any future caching code in this project:** never hand `Cache::remember()`/`rememberForever()` a raw Eloquent `Collection` or `Model` on the `database` driver. Flatten to arrays/scalars first.

### Boot-time gotcha: defer DB-dependent provider logic to `booted()`, not `boot()`

Debugging the above also surfaced a second, related issue: `AppServiceProvider::boot()` originally called `Setting::get('mail_host')` directly (to apply stored SMTP settings to the runtime mail config). Calling an Eloquent query that early in the provider boot sequence made the corrupted-cache symptom appear *before* the app had even fully booted, which is why it initially looked like a boot-order problem rather than a caching one. It's now wrapped in `$this->app->booted(fn () => $this->applyMailSettings())` — `booted()` fires once every provider has finished booting, which is the safe point for this kind of cross-cutting DB read. Kept even after the real fix (flattening the cache value), since running an unnecessary Eloquent query at the very top of `boot()` on every single request/command is bad practice regardless.

One more consequence worth knowing: if a corrupted cache row already exists, *every* artisan command — including `cache:clear` itself — will crash on boot before your own command logic runs, because provider boot happens first. The only way out is deleting the row directly (`DELETE FROM cache WHERE \`key\` LIKE '%app_settings%'` via the MySQL client, bypassing Laravel's bootstrap entirely).

### SMTP settings actually take effect — and the password is encrypted at rest

`AppServiceProvider::applyMailSettings()` overrides `config('mail.mailers.smtp.*')` and `config('mail.from.*')` from stored settings, but only if `mail_host` is actually set (so a fresh install keeps the `.env`-configured `log` driver locally). `mail_password` is `Crypt::encryptString()`'d before storage and decrypted only when applying the runtime config — it is never echoed back into the settings form; leaving that field blank on a re-save keeps the existing password rather than wiping it. Verified: encrypted-at-rest, round-trips correctly, survives a blank re-save, and the live `config('mail.mailers.smtp.*')` reflects it after saving.

**Not yet filled in with real credentials** — `mail_host`/`username`/`password` are whatever's entered in the admin UI; see [inquiries.md](inquiries.md) for where this is actually used (the contact-form notification email).

### SEO fallback resolution

`App\Common\Services\SeoService::resolve(?Model $entity = null): array` returns `title`/`description`/`keywords`/`og_image`/`twitter_handle`, each falling back: entity's own `meta_*` → entity's own `title`/`summary` → global `Setting` → `config('app.name')` (title only). Guards against `Model::shouldBeStrict()` throwing on an entity that doesn't have a given attribute at all (e.g. `Gallery` has no `meta_keywords`) the same way `HasSeoMeta::seoAttr()` does — duplicated rather than shared because this resolver must also work with `$entity = null` (a plain static page) or an entity that doesn't use that trait.

`resources/views/components/web/seo-head.blade.php` — a real `<x-web.seo-head :entity="$post" />` tag component (unlike most of this project's shared partials, which are `@include`d with explicit data) — renders `<title>`, meta description/keywords, canonical URL, OpenGraph, and Twitter Card tags from the resolved values, plus Google Search Console verification if set. **Not wired into any public page yet** — no public-facing Blade layout exists to use it in (see [cms-sections.md](cms-sections.md) — only the admin side of content exists so far).

### Admin UI

`App\Http\Controllers\Admin\SettingController` — not a resource controller (settings aren't a list of records to CRUD, they're one global set), just `index`/`update`. Each tab is a separate `<form>` posting its own `group` plus only that group's fields; `update()` whitelists which keys each group may write (`GROUP_KEYS` constant) so a crafted request can't set an arbitrary settings key. Gated behind `manage-settings` (seeded onto the `admin` role).

## Explicitly not built yet

- Sitemap/robots.txt/llms.txt cache invalidation (`Cache::forget('seo_sitemap_xml')` is already called on every settings save, pre-emptively, but nothing produces that cache key yet — no sitemap controller exists).
- The OG default image upload stores a file via `Storage::disk('public')` (not Spatie Media) — a deliberate simpler choice since `Setting` isn't an `HasMedia` model and adding that relationship just for one image felt like the wrong shape for a key-value settings row.
- No "Flush SEO Cache" button yet — cache invalidation currently only happens automatically on settings save.
