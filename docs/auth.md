# Authentication

## Current state (as of 2026-10-02)

Basic session login is implemented — no package (no Breeze/Jetstream/Fortify), hand-rolled using the project's Action/DTO/thin-controller convention. There's only one account type (admin); the "public site" has no accounts of its own, so login/logout live under the admin portal's own `/admin` namespace rather than at the root.

- `GET /admin/login` — `App\Http\Controllers\Auth\LoginController@create`, named route `login` (not `admin.login` — see below). Guarded by `guest`.
- `POST /admin/login` — `App\Http\Controllers\Auth\LoginController@store`. Validates via `App\Http\Requests\Auth\LoginRequest`, builds `App\DTOs\Auth\LoginData`, delegates the actual attempt to `App\Actions\Auth\AttemptLoginAction`.
- `POST /admin/logout` — `App\Http\Controllers\Auth\LoginController@destroy`, named route `logout`. Guarded by `auth`.
- These three routes are named `login`/`logout`, **not** `admin.login`/`admin.logout`, even though their URIs live under `/admin` — deliberately. Laravel's default `Authenticate` middleware redirects unauthenticated requests via a hardcoded `route('login')` call; renaming the route would break that redirect (it'd throw `RouteNotFoundException`) unless the middleware were overridden. Route name and URI are independent in Laravel, so the URI moved under `/admin` without touching the name.
- Login throttling: 5 attempts per `email|ip` key via `RateLimiter`, inside `AttemptLoginAction`.
- Session is regenerated on successful login and invalidated on logout (session fixation protection).
- On success, redirects to `redirect()->intended(route('admin.dashboard'))` — so a login with no prior intended URL lands on the dashboard, not the public homepage; a login triggered by hitting a protected admin page while logged out still returns to that exact page.
- The public homepage (`resources/views/welcome.blade.php`) shows a "Sign in" link (→ `/admin/login`) when logged out and a "Sign out" button (→ `/admin/logout`) when logged in.
- The sign-in page itself is the Hope UI-styled split-screen design (see [admin-ui.md](admin-ui.md)).

### Admin area authorization (as of 2026-10-02)

`spatie/laravel-permission`'s `HasRoles` is on `User`, and it's now actually wired into route protection — see [admin-ui.md](admin-ui.md) for the full picture:

- `GET /admin` (`admin.dashboard`) — behind `auth` only, any logged-in user can reach it.
- `GET /admin/roles` (`admin.roles.index`) — behind `auth` **and** `permission:manage-roles`; a placeholder page that exists to prove the permission check and the sidebar's permission-filtering are both real, not just a page with nothing to check. Verified both ways (403 without the permission, 200 and visible in the sidebar with it).
- `PermissionMiddleware`/`RoleMiddleware`/`RoleOrPermissionMiddleware` had to be registered by hand as middleware aliases in `bootstrap/app.php` — the package doesn't self-register them for a Laravel 11+-style `bootstrap/app.php` (no `Kernel.php` in this project).
- The `admin` role bypasses **every** permission check, everywhere — `App\Providers\AppServiceProvider::boot()` registers `Gate::before(fn ($user, $ability) => $user->hasRole('admin') ? true : null)`. An admin user never needs an individual permission assigned to see a page; `$user->can('anything-at-all')` is `true` for them, including abilities that don't even exist as real permissions. Returning `null` (not `false`) for non-admins is what lets normal permission checks still apply to everyone else — verified both sides: an admin passes a permission check it was never explicitly granted, a non-admin is still correctly denied one it lacks. This applies uniformly to `$user->can()`, `@can` in Blade, and the `permission:`/`role:` route middleware, since they all route through Laravel's Gate.

### Seeded admin account

`database/seeders/AdminUserSeeder.php` (called from `DatabaseSeeder`) creates a reproducible local admin account: **`admin@example.com` / `password`**, assigned the `admin` role, which holds `manage-roles`. Run `php artisan db:seed` after a fresh migration to get it back — it's `firstOrCreate`, safe to re-run.

One gotcha worth knowing if this seeder is ever extended: Spatie caches the role/permission list (`PermissionRegistrar`). If a permission was deleted in a previous run (e.g. ad-hoc testing) and the seeder creates a new one with a different id, `givePermissionTo()` can resolve the *stale cached* permission and insert a foreign key that no longer exists. The seeder calls `app(PermissionRegistrar::class)->forgetCachedPermissions()` first to avoid this — don't drop that line.

## Explicitly not built yet

- No registration, password reset, or email verification flow — and the sign-in page deliberately has no links to any of these (no dead links to features that don't exist).
- No actual roles/permissions management UI — `admin.roles.index` is a placeholder, not real CRUD.
- No API authentication (Sanctum/Passport) — only session-based web auth exists. This is also why the theme-toggle endpoint lives in `routes/web.php` despite its `api/...` URL — see [admin-ui.md](admin-ui.md).
