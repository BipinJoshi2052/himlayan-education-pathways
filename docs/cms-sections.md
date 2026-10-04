# Dynamic Sections & Content Blocks (CMS)

## Current state (as of 2026-10-02)

A schema-agnostic content block system — one pair of tables (`sections` / `section_items`) backs any page component (hero, director's message, stats grid, about cards, ...) instead of a dedicated table per component type.

### Schema

- `sections` — `id` (UUID), `page_slug` (indexed), `key` (unique **per** `page_slug`, not globally), `layout_type`, `title`/`subtitle`/`content` (translatable JSON), `settings` (plain JSON), `is_active`, `sort_order`, timestamps, **soft deletes**.
- `section_items` — `id` (UUID), `section_id` (UUID FK, cascade delete), `title`/`description` (translatable JSON), `icon_or_badge`, `link_url`, `sort_order`, timestamps. No soft deletes on items — see below.
- `App\Enums\SectionLayoutType`: `SingleBlock` / `Repeater` / `Cards`. Stored as a plain string column, cast to this enum on the model (`usesItems()` tells you whether a layout type manages `section_items` at all — currently informational only, not yet enforced anywhere).

### Models

- `App\Models\Section` — `HasUuidPrimaryKey`, `HasTranslations` (`title`, `subtitle`, `content`), `InteractsWithMedia` (implements `HasMedia`), `SoftDeletes`. `items()` → `hasMany(SectionItem::class)->orderBy('sort_order')`. `scopeActive()` for the eventual public-facing query.
- `App\Models\SectionItem` — same UUID/translatable/media stack (`title`, `description` translatable), `belongsTo(Section::class)`.
- Translatable fields are read/written as plain strings via the current app locale (`$model->title = 'Hello'` → stored as `{"en": "Hello"}`) — see "Single-locale admin UI" below.

### Soft deletes and the `(page_slug, key)` uniqueness

The DB has a plain composite unique index on `(page_slug, key)`. Soft-deleting a section does **not** free up its slug/key for reuse at the database level — the row still physically exists with `deleted_at` set, so the unique index still blocks a duplicate. (The common workaround of adding `deleted_at` to the unique index doesn't actually work on MySQL: MySQL treats every `NULL` as distinct for uniqueness purposes, so two *active* rows — both with `deleted_at IS NULL` — wouldn't collide at all, silently defeating the constraint among the rows that matter most.) The validation rule (`SectionRequest`) layers `->whereNull('deleted_at')` on top of `Rule::unique()` so the user gets a proper validation error day-to-day, but reusing a slug/key after soft-deleting its section currently requires restoring or force-deleting the old row first. No restore UI exists yet.

Soft-deleting a section leaves its `section_items` rows in place (there's no cascade on soft delete, only on a real `DELETE`) — verified directly: delete a section, its items are still readable by `section_id`. This is almost certainly the right default (data preserved, restorable), but there's currently no "restore" action anywhere in the admin UI to act on it.

### Admin CRUD

`App\Http\Controllers\Admin\SectionController` (`index`/`create`/`store`/`edit`/`update`/`destroy`, resourceful, `except('show')`) behind `permission:manage-sections`. Thin controller — validation lives in `App\Http\Requests\Cms\SectionRequest`, the actual create-or-update-and-sync-items logic lives in `App\Actions\Cms\SaveSectionAction::handle()`, which both `store` and `update` call (it creates a new `Section` when none is passed in, updates in place otherwise — one action, not two, since the "sync items" logic is identical either way).

**Item sync logic** (in the Action): every submitted item with an `id` matching one of the section's existing items is updated in place; every submitted item without an `id` (or whose `id` doesn't match) is created; every existing item whose `id` wasn't present in the submission is deleted. Verified end-to-end with a real edit: modifying one item kept its database ID (not delete+recreate), removing an item from the form actually deleted it, and adding a new row created it — in the same request.

**Repeater UI**: `resources/views/admin/sections/_form.blade.php` + `partials/item-row.blade.php`. Rows are added/removed client-side with plain vanilla JS (an HTML `<template>` element, cloned and index-substituted) — no build step, consistent with the rest of the admin UI.

**A real bug caught and fixed, not just assumed away**: on a validation error (e.g. missing section title), the form redisplay must repopulate the repeater from the *resubmitted* `old('items')` input, not from the section's saved DB items — otherwise a validation error on an otherwise-fine repeater would silently wipe out everything the admin had just typed into it. Verified directly: submitted a new section with one item and a missing required field, got redirected back, and confirmed the item's title/description were still in the redisplayed form.

### Spatie QueryBuilder — first real use in this project

`SectionController::index()` is the first place `spatie/laravel-query-builder` (installed since Phase 1, unused until now) actually does something: `?filter[page_slug]=home` filters the list, with `page_slug`/`key`/`is_active` as allowed filters and `page_slug`/`sort_order`/`created_at` as allowed sorts.

**Gotcha hit during this phase**: `allowedFilters()` and `allowedSorts()` are variadic (`...$filters` / `...$sorts`), not array-accepting — passing an array as a single argument (`allowedFilters(['a', 'b'])`) throws a `TypeError` at runtime, not a static-analysis-catchable mistake. Call them as `allowedFilters('a', 'b', AllowedFilter::exact('c'))` instead.

### Single-locale admin UI (deliberate scope boundary)

The schema fully supports multi-language content (JSON translatable columns), but the admin form only edits the app's current locale (`config('app.locale')`, currently `en`) — there's no language-tab UI to edit multiple locales side by side. This was a deliberate scope call for this phase, not an oversight: building a proper multi-locale editing UI is a separable feature. When it's needed, the schema doesn't need to change — only the form and the DTOs need to start carrying a locale dimension.

### Permissions & navigation

New permission: `manage-sections` (seeded onto the `admin` role in `AdminUserSeeder`, though the `admin` role would bypass this check regardless — see [auth.md](auth.md)). Sidebar entry "Content Sections" added to `AdminNavigationService`, using a new `NavItem::activePattern` field (defaults to the item's own route name) so the sidebar correctly shows the item as active across all of `admin.sections.*` (index/create/edit), not just the exact index route — a real gap the single-route Dashboard/Roles items never exposed, since they only ever link to one route each.

### Also fixed in this phase (not CMS-specific, but needed for the index page to render)

Laravel's default pagination view is Tailwind-based; this admin UI has no Tailwind anywhere (Bootstrap 5 / Hope UI throughout — see [admin-ui.md](admin-ui.md)), so it would have rendered unstyled. `Paginator::useBootstrapFive()` added globally in `AppServiceProvider::boot()`.

## Explicitly not built yet

- No public-facing rendering of sections (no Blade component that takes a `Section` and renders it per `layout_type` on the actual public site pages).
- No media upload UI — `InteractsWithMedia` is on both models per the phase spec, but nothing in the admin form lets you attach an image yet.
- No multi-locale editing UI (see above).
- No restore-from-trash UI for soft-deleted sections.
- No enforcement that `layout_type` actually matches what the form captures (e.g. nothing stops creating a `single_block` section with items attached, or a `repeater` section with none) — the enum's `usesItems()` helper exists for this but isn't wired into validation yet.
