<?php

declare(strict_types=1);

namespace App\Common\Services;

use App\Models\Setting;

/**
 * config('app.available_locales') is the full catalog of locales the app
 * knows how to render at all. Settings > Languages lets an admin narrow
 * that catalog down separately for the public site (which locales show in
 * the website's language switcher) and the admin panel (which locale tabs
 * appear when editing translatable content) — see docs/admin-ui.md.
 */
final class LocaleOptions
{
    /**
     * locale code => flag-icons (https://github.com/lipis/flag-icons)
     * country code. English maps to GB rather than US — no reason to
     * prefer one over the other for this app, GB is just the more common
     * default pairing for "English" internationally.
     *
     * @var array<string, string>
     */
    private const FLAG_CODES = [
        'en' => 'gb',
        'ne' => 'np',
        'de' => 'de',
    ];

    public static function flagCode(string $locale): ?string
    {
        return self::FLAG_CODES[$locale] ?? null;
    }

    /**
     * @return array<string, string> locale code => label
     */
    public static function forWeb(): array
    {
        return self::filtered('locales_web');
    }

    /**
     * @return array<string, string> locale code => label
     */
    public static function forAdmin(): array
    {
        return self::filtered('locales_admin');
    }

    /**
     * @return array<string, string>
     */
    private static function filtered(string $settingKey): array
    {
        $all = config('app.available_locales');
        $enabled = Setting::get($settingKey);

        if (! is_array($enabled) || $enabled === []) {
            return $all;
        }

        return array_intersect_key($all, array_flip($enabled));
    }
}
