<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(! $this->app->isProduction());

        // The 'admin' role bypasses every permission check — it should never
        // need individual permissions assigned to see any page. Returning
        // null (not false) for non-admins lets normal permission checks run.
        Gate::before(fn (User $user, string $ability) => $user->hasRole('admin') ? true : null);

        // The admin UI is Bootstrap 5 (Hope UI) throughout; Laravel's default
        // pagination view is Tailwind-based and would render unstyled here.
        Paginator::useBootstrapFive();

        // Deferred to booted() rather than called directly here: this does
        // an Eloquent query, and running that this early in boot() (before
        // every provider has finished booting) caused a PHP-level
        // "incomplete object" unserialize error reading the settings cache —
        // a class-loading-order issue specific to querying the DB from
        // inside boot(). booted() runs once the whole framework is ready.
        $this->app->booted(fn () => $this->applyMailSettings());
    }

    /**
     * Admin-configured SMTP settings (Settings > SMTP/Mail) override the
     * .env mail config at runtime — otherwise that admin tab would just be
     * cosmetic and never actually affect what Laravel sends with. Guarded
     * against the settings table not existing yet (fresh install, before
     * migrations have run) and against no host being configured at all
     * (keeps the .env-configured default, e.g. the 'log' driver locally).
     */
    private function applyMailSettings(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $host = Setting::get('mail_host');

        if (blank($host)) {
            return;
        }

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => $host,
            'mail.mailers.smtp.port' => Setting::get('mail_port', 587),
            'mail.mailers.smtp.username' => Setting::get('mail_username'),
            'mail.mailers.smtp.password' => Setting::get('mail_password')
                ? Crypt::decryptString(Setting::get('mail_password'))
                : null,
            'mail.mailers.smtp.encryption' => Setting::get('mail_encryption', 'tls') ?: null,
            'mail.from.address' => Setting::get('mail_from_address', config('mail.from.address')),
            'mail.from.name' => Setting::get('mail_from_name', config('mail.from.name')),
        ]);
    }
}
