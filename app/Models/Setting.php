<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Deliberate exception to the project's "every model uses a UUID primary
 * key" rule (see docs/architecture.md): a settings row's natural identity
 * IS its key ('seo_meta_title', 'site_name', ...) — a human-readable string
 * primary key, same category of exception already made for Role/Permission.
 */
class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'group', 'value'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'json',
        ];
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        // Cache a plain array, not a raw Eloquent Collection of models.
        // Caching model instances through the database cache driver's
        // serialize()/unserialize() round-trip failed reproducibly in this
        // environment ("incomplete object" on unserialize) — Spatie's own
        // PermissionRegistrar avoids the same thing by explicitly
        // flattening to an array before caching; this does the same.
        $all = Cache::rememberForever('app_settings', fn () => static::query()->pluck('value', 'key')->all());

        return $all[$key] ?? $default;
    }

    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);

        Cache::forget('app_settings');
    }

    /**
     * $value is expected to be a locale => value array for a translatable
     * setting (e.g. ['en' => 'Hello', 'ne' => '...']) — falls back to the
     * app's fallback locale, then to $default, same precedence as
     * HasTranslations itself.
     */
    public static function getTranslatable(string $key, ?string $locale = null, mixed $default = null): mixed
    {
        $value = static::get($key);

        if (! is_array($value)) {
            return $value ?? $default;
        }

        $locale ??= app()->getLocale();

        return $value[$locale]
            ?? $value[config('app.fallback_locale')]
            ?? $default;
    }
}
