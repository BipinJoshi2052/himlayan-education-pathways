# Admin UI (Hope UI integration)

## Current state (as of 2026-10-02)

The admin area and the public sign-in page are built on **Hope UI** (a Bootstrap 5 admin template), integrated as static CSS/JS served from `public/`, rendered through server-side Blade — no frontend framework, no build step, consistent with [architecture.md](architecture.md).

The source template lives at `/admin-design-template` in the project root (git-ignored, local reference only — see that file's note in architecture.md). Only the specific files actually needed were copied out of it into `public/`; the rest of that 24MB template was never copied in.

### ⚠️ Asset path: `public/admin-assets/`, never `public/admin/`

This is the one mistake most likely to get reintroduced, so it's called out on its own:

**Do not create a `public/admin/` directory.** Both the PHP built-in dev server and a standard Apache/Nginx + `.htaccess` setup check whether a request path matches an existing file **or directory** on disk before handing the request to Laravel's router. The app has a real `/admin` route (the dashboard). If `public/admin/` exists as a directory — even empty, even just for assets — every request under `/admin` gets intercepted and resolved against that directory instead of ever reaching Laravel, producing a raw webserver 404 instead of the actual page. This happened once during development; the fix was moving everything to `public/admin-assets/` instead, which can't collide with any `/admin*` route.

If a future admin sub-route is added (e.g. `/admin/users`), the same rule applies — never let a static directory under `public/` shadow a route path.

### What was copied in, and why only this much

From `/admin-design-template/dist/assets/`:

| File | Why |
|---|---|
| `css/hope-ui.min.css` | The actual Bootstrap 5 + Hope UI theme build — layout, components, utilities, icon sizing. Self-contained (icons are inline SVG or data-URIs, no external font files). |
| `css/custom.min.css` | Small (15KB) supplementary overrides Hope UI ships alongside the main CSS on every page. |
| `js/core/libs.min.js` → `js/libs.min.js` | Bundled jQuery 3.7.1 + Bootstrap's JS (incl. Popper) — needed for dropdowns, collapse (sidebar submenu), etc. |
| `js/hope-ui.js` | Hope UI's own script: sidebar collapse/mini toggle, mobile overlay. |
| `images/auth/01.png` | The split-screen hero image on the sign-in page. |

Deliberately **not** copied (and why):

- `css/core/libs.min.css` — third-party plugin styles (Leaflet maps, etc.) for demo pages this project doesn't use; would only add dead 404s for images it references.
- `css/customizer.min.css`, `css/rtl.min.css` — the full theme customizer (color/layout picker panel) and RTL support were not built; only a simple dark/light toggle button exists (see below).
- `js/core/external.min.js` — bundles ApexCharts; nothing in this project renders a chart yet.
- `js/charts/*`, `js/plugins/*` (fslightbox, setting.js, slider-tabs, form-wizard), `vendor/aos` — all demo-page-specific, unused here. `plugins/setting.js` in particular is what drives Hope UI's full customizer panel, which this project intentionally doesn't have.
- The demo avatar image (`images/avatars/01.png`, 2.4MB stock photo) — replaced with a CSS-only initials circle (`.avatar-initial` in `admin-assets/css/admin.css`) since no real avatar upload exists yet (medialibrary isn't wired to `User`).

### Layout structure

- `resources/views/layouts/admin.blade.php` — master layout for authenticated admin pages. Sets `data-bs-theme` and `--bs-primary` on `<html>` from the logged-in user's `theme_mode`/`primary_color` columns (added in [Phase 1](architecture.md), falling back to `'light'` / `'#3a57e8'` — confirmed to be Hope UI's own shipped defaults, not arbitrary values, by checking `--bs-primary` inside `hope-ui.min.css` directly). `secondary_color` exists on the schema but isn't wired into the layout yet — only `primary_color` was asked for.
- `resources/views/components/admin/sidebar.blade.php` — renders `App\Services\AdminNavigationService::visibleTo(auth()->user())`, marking the active item via `request()->routeIs()`.
- `resources/views/components/admin/navbar.blade.php` — dark/light toggle button + user dropdown (initials avatar, name, sign-out).
- `resources/views/admin/dashboard.blade.php` — `@extends('layouts.admin')`, three static metric cards (Total Visits / Active Courses / Inquiries — sample data, not wired to anything real).
- `resources/views/login.blade.php` — **not** wrapped in `layouts.admin` (it's pre-auth, full-bleed split-screen design, structurally different from the internal admin shell). Rebuilt directly from Hope UI's `dashboard/auth/sign-in.html`. Dropped from the original template: the "sign in with other accounts" social buttons and the "sign up" / "forgot password" links — none of that backend exists (see [auth.md](auth.md)), and a dead link is worse than no link. Kept: email/password/remember-me, which the backend already supports.

### Sidebar navigation & permission filtering

`App\Services\AdminNavigationService` is the single source of truth for sidebar items — add a new `App\DTOs\Admin\NavItem` to its `items()` method for each new admin page. Each item has an optional `permission`; `NavItem::isVisibleTo()` checks `$user->can($permission)`, and items with no permission are visible to any authenticated user. `visibleTo()` filters and is what the sidebar component actually calls — the menu is never rendered unfiltered.

Verified end-to-end (not just by reading the code): a user without the `manage-roles` permission gets only "Dashboard" in the sidebar and a 403 on `GET /admin/roles`; granting the permission makes the item appear and the route return 200.

`Spatie\Permission\Middleware\PermissionMiddleware` (and `RoleMiddleware`, `RoleOrPermissionMiddleware`) had to be registered by hand as `permission:`/`role:`/`role_or_permission:` middleware aliases in `bootstrap/app.php` — `spatie/laravel-permission` v8 does not self-register them under Laravel 11+'s `bootstrap/app.php` style the way an old `Kernel.php` based project would get them for free.

`App\Http\Controllers\Admin\RoleController` is a placeholder — it exists only so the permission-gated sidebar item points at something real and testable, not a dead link. The actual roles & permissions management UI is a future phase.

### Dark/light theme toggle

`POST /api/admin/profile/theme-toggle` flips the logged-in user's `theme_mode` between `light`/`dark` and persists it (`App\Actions\Admin\ToggleThemeModeAction`), returning the standard [`ApiResponse`](api-responses.md) envelope. The navbar button calls it via jQuery and updates `data-bs-theme` on `<html>` immediately from the response, so there's no page reload.

**This route is registered in `routes/web.php`, not `routes/api.php`**, despite its URL starting with `api/`. `routes/api.php` has no session middleware unless Sanctum's stateful-API mode is configured (it isn't — this project only has plain session auth, see [auth.md](auth.md)), so a route actually declared there could never see `auth()->user()`; it would 401 unconditionally. Registering it in `web.php` with an explicit `api/...` URI gets session + CSRF + the `auth` middleware (so it actually works) while still matching the literal path and still going through the `ApiResponse`/exception-handling machinery in `bootstrap/app.php`, since that's keyed off the request path (`is('api/*')`), not which routes file registered it.

AJAX CSRF is wired via a `<meta name="csrf-token">` tag in the layout head and `$.ajaxSetup` in `admin-assets/js/admin.js`, the standard Laravel + jQuery pattern.

### `.btn-primary` disappearing on hover — a gap in the shipped CSS, not something we broke

`hope-ui.min.css` defines `.btn-primary`'s hover/active background as `var(--bs-primary-hover-bg)` / `var(--bs-primary-active-bg)` — but never defines those two custom properties anywhere in the files we copied in. They're only ever written at runtime by the full customizer panel (`plugins/setting.js`), which this project deliberately doesn't include (see above). Without a value, the hover background is an invalid `var()` reference, which computes to `transparent` — the button background vanishes on hover/active, text still white-on-white-ish. This isn't something our integration broke; the stock template has the same latent gap whenever that customizer script isn't present.

Fixed in `admin-assets/css/admin.css` by deriving both variables from `--bs-primary` with `color-mix()`, so it works for whatever color a user has set, not just the default:

```css
--bs-primary-hover-bg: color-mix(in srgb, var(--bs-primary) 85%, black);
--bs-primary-active-bg: color-mix(in srgb, var(--bs-primary) 75%, black);
```

This file must be loaded on **every** page that uses Bootstrap's primary button — both `layouts/admin.blade.php` and the standalone `login.blade.php` include it.

### Password visibility toggle

Hope UI's own auth pages (checked all of them) don't ship a show/hide-password control. Added one to `login.blade.php`: an eye icon absolutely positioned inside the password field (`.password-toggle-icon` in `admin.css`), toggled with a small inline vanilla-JS script swapping the input's `type` and the icon. Not part of the vendored JS — ours.
