# Contact Form & Inquiries

## Current state (as of 2026-10-02)

- `POST /contact/send` (public, `throttle:5,1` — verified: 6th request in a minute gets `429`) — `App\Http\Controllers\Web\ContactController@send`. Validates via `ContactRequest`, builds `App\DTOs\Contact\InquiryData`, delegates to `App\Actions\Contact\SubmitInquiryAction`, responds with the standard [`ApiResponse`](api-responses.md) envelope.
- **No public contact form page exists yet** — only the submission endpoint. Tested directly via POST (curl), not through a rendered form. A real contact page is a separate, not-yet-built piece.
- `App\Models\Inquiry` — `HasUuidPrimaryKey` + `HandlesTrash` (see [architecture.md](architecture.md) / the trash standardization work). `user_agent` was added to the migration beyond the originally listed columns — the admin detail page explicitly needed to display it and no other column could supply it.
- `App\Notifications\NewInquiryReceivedNotification` — queued (`ShouldQueue`), delivered via on-demand routing (`Notification::route('mail', $recipient)->notify(...)`) rather than to a `User` model, since the recipient is a configured email address, not necessarily an account. Recipient resolution: `Setting::get('admin_notification_email')` (Settings → General tab) → falls back to the first user with the `admin` role → if neither exists, silently skips sending rather than erroring (there's always *something* to notify once the seeded admin exists, so this fallback is mostly a safety net).
- Verified end-to-end: submitted via curl → `Inquiry` row created with correct IP/user-agent → job appears in the `jobs` table → `php artisan queue:work --stop-when-empty` processes it → the `log`-driver mailer (local `.env` default) shows a correctly addressed, correctly subjected email in `storage/logs/laravel.log`.

### Admin inbox

`App\Http\Controllers\Admin\InquiryController` — `index` (mail-style list, unread badge, search/status filter, trash tab), `show` (marks `unread` → `read` as a side effect of viewing — verified), `updateNotes` (one combined form for status + internal notes, not a generic update — there's no "edit the inquiry's own content" concept, only status and admin notes), `destroy` (soft delete), plus `restore`/`forceDelete` via `HasTrashActions`. Gated behind `manage-inquiries`.

No "edit" route exists for inquiries (there's nothing to edit on an inbox message beyond status/notes) — the shared `admin.partials.row-actions` partial needed a `showEdit: false` option added to accommodate this, since every other trashable module *does* have an edit page.

## Explicitly not built yet

- No public contact form page/view.
- No reply-from-admin-panel feature — "Replied" is just a status an admin sets manually after replying by email themselves.
- Real SMTP credentials — see [settings.md](settings.md). Locally this uses the `log` mail driver; nothing has actually been sent to a real inbox.
