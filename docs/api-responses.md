# API Response Envelope & Exception Handling

## Current state (as of 2026-10-02)

All JSON/API responses use a single envelope shape, built by `App\Common\Helpers\ApiResponse` ([app/Common/Helpers/ApiResponse.php](../app/Common/Helpers/ApiResponse.php)):

```json
// success
{ "success": true, "status_code": 200, "message": "...", "data": ..., "meta": {}, "error": null }

// error
{ "success": false, "status_code": 400, "message": "...", "data": null, "meta": {}, "error": { "code": "BAD_REQUEST", "details": ... } }
```

`meta` is always serialized as a JSON object (`{}` when empty), never an array, even though it's typed as a plain PHP array internally — handled by casting empty arrays to `(object) []` before encoding.

Use `ApiResponse::success(...)` / `ApiResponse::error(...)` directly from controllers/actions whenever returning JSON — don't hand-build the envelope inline.

## Centralized exception handling

Wired in `bootstrap/app.php` via `->withExceptions()`, **not** a `Handler.php` class (this is the Laravel 11+ style — there's no `app/Exceptions/Handler.php` in this project).

A request is treated as "wants JSON" if it's under `api/*` or sends `Accept: application/json` (`$wantsJson` closure in `bootstrap/app.php`). For such requests:

| Exception | Status | Code | Notes |
|---|---|---|---|
| `Illuminate\Validation\ValidationException` | 422 | `VALIDATION_ERROR` | `details` is the field→messages array from `$e->errors()`. |
| `Symfony\Component\HttpKernel\Exception\NotFoundHttpException` | 404 | `NOT_FOUND` | Covers both unmatched routes and `findOrFail()`-style 404s. |
| `Illuminate\Auth\AuthenticationException` | 401 | `UNAUTHENTICATED` | Fires when `auth` middleware rejects an unauthenticated request to an `api/*` route. |
| Anything else (`Throwable`) | 500 | `SERVER_ERROR` | Message is the real exception message outside production, and a generic masked message (`"Something went wrong..."`) in production. |

Non-JSON (regular web) requests are untouched by these renderers — they fall through to Laravel's normal behavior (redirect-with-errors for validation, the standard error pages for 404/401/500). The web login form ([auth.md](auth.md)) still works exactly as before.

**Renderer order matters:** the four `$exceptions->render(...)` callbacks are registered most-specific-first, ending with the general `Throwable` catch-all. Laravel tries each in registration order and uses the first one whose type matches *and* returns a non-null response — so the specific handlers must come before the generic one, or the generic one would swallow everything.

## Verifying it

Test routes in `routes/api.php` exercise every path above:

- `GET /api/ping` — success envelope
- `GET /api/ping/validation-error` — 422
- `GET /api/does-not-exist` (any unmatched `api/*` path) — 404
- `GET /api/ping/auth` (no session) — 401
- `GET /api/ping/server-error` — 500

These are verification routes, not real endpoints — remove or repurpose them once real API endpoints exist.
