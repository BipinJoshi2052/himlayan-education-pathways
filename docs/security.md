# Security

## Current state (as of 2026-10-02)

Nothing beyond Laravel's framework-level defaults has been added yet. This file is a placeholder to be filled in as security-relevant decisions are made — see [auth.md](auth.md) for what little auth exists so far.

What's in place today, incidentally, via the framework and the login implementation:

- CSRF protection on all state-changing form submissions (`@csrf` + Laravel's default `VerifyCsrfToken` middleware).
- Passwords hashed via the `hashed` cast on `User::password` (bcrypt, `BCRYPT_ROUNDS=12`).
- Login throttled at 5 attempts per email+IP (see [auth.md](auth.md)).
- Session fixation protection: session regenerated on login, invalidated on logout.
- API/JSON error responses mask the real exception message in production (generic "Something went wrong..." instead of `$e->getMessage()`), so internal details aren't leaked to clients — see [api-responses.md](api-responses.md). Outside production the real message is shown, deliberately, to help local/staging debugging.

## Not yet addressed

- No formal threat model, no dependency/vulnerability scanning process documented.
- `spatie/laravel-permission` is installed and `User` has `HasRoles`, but no route or controller actually checks a role/permission yet — see [auth.md](auth.md) and [packages.md](packages.md).
- No rate limiting beyond the login endpoint.
- No security headers policy (CSP, HSTS, etc.) documented or configured.
- No audit logging.

Update this file as each of these is decided or implemented, rather than treating it as a one-time document.
