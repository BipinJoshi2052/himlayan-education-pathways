<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Common\Services\LocaleOptions;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Spatie\Image\Image;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

final class SettingController extends Controller
{
    /**
     * Keys each group is allowed to write. A request can only ever set
     * keys listed here — not an arbitrary key an attacker might slip in.
     *
     * @var array<string, array<int, string>>
     */
    private const GROUP_KEYS = [
        'general' => ['site_name', 'site_tagline', 'site_address', 'google_maps_url', 'site_logo', 'contact_email', 'contact_phone', 'admin_notification_email', 'map_latitude', 'map_longitude'],
        'seo' => ['seo_meta_title', 'seo_meta_description', 'seo_meta_keywords', 'seo_default_og_image', 'google_site_verification', 'google_analytics_id'],
        'social' => ['social_facebook_url', 'facebook_page_id', 'facebook_page_token', 'social_instagram_url', 'social_linkedin_url', 'social_youtube_url', 'social_tiktok_url', 'tiktok_video_links', 'social_twitter_handle', 'twitter_card_type'],
        'smtp' => ['mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name'],
        'appearance' => ['primary_color', 'secondary_color'],
        'languages' => ['locales_web', 'locales_admin'],
        'loader' => ['loader_mode', 'loader_image'],
        'widgets' => ['whatsapp_enabled', 'whatsapp_phone'],
    ];

    private const TRANSLATABLE_KEYS = ['seo_meta_title', 'seo_meta_description', 'seo_meta_keywords'];

    private const FILE_KEYS = ['seo_default_og_image', 'site_logo', 'loader_image'];

    private const BOOLEAN_KEYS = ['whatsapp_enabled'];

    public function index(): View
    {
        return view('admin.settings.index', [
            'locales' => array_keys(LocaleOptions::forAdmin()),
            'allLocales' => config('app.available_locales'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $group = $request->input('group');

        if (! array_key_exists($group, self::GROUP_KEYS)) {
            abort(422, 'Unknown settings group.');
        }

        foreach (self::GROUP_KEYS[$group] as $key) {
            // An unchecked checkbox submits no key at all, so this has to be
            // handled before the generic `! $request->has($key)` skip below
            // — otherwise turning a toggle OFF would never actually save.
            if (in_array($key, self::BOOLEAN_KEYS, true)) {
                Setting::set($key, $request->boolean($key), $group);

                continue;
            }

            if (in_array($key, self::FILE_KEYS, true)) {
                if ($request->hasFile($key)) {
                    $path = $request->file($key)->store('settings', 'public');
                    Setting::set($key, Storage::url($path), $group);

                    if ($key === 'site_logo') {
                        $this->saveWebLogo($path, $group);
                    }
                }

                continue;
            }

            // Leaving the password field blank on an edit keeps the existing
            // one — re-saving the form shouldn't wipe a working credential —
            // and it's encrypted at rest; this table has no other protection
            // for it (see docs/settings.md).
            // The Facebook Page token is a secret, like the mail password: stored
            // encrypted, and a blank field keeps the saved one.
            if ($key === 'facebook_page_token') {
                if ($request->filled($key)) {
                    Setting::set($key, Crypt::encryptString($request->input($key)), $group);
                }

                continue;
            }

            if ($key === 'mail_password') {
                if ($request->filled($key)) {
                    Setting::set($key, Crypt::encryptString($request->input($key)), $group);
                }

                continue;
            }

            if (! $request->has($key)) {
                continue;
            }

            $value = in_array($key, self::TRANSLATABLE_KEYS, true)
                ? $request->input($key, [])
                : $request->input($key);

            Setting::set($key, $value, $group);
        }

        Cache::forget('app_settings');
        Cache::forget('facebook_feed_posts');
        Cache::forget('facebook_page_stats');
        $this->flushSeoCacheKeys();

        return redirect()->route('admin.settings.index', ['tab' => $group])->with('status', 'Settings saved.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'different:current_password', Password::defaults()],
        ]);

        $request->user()->update(['password' => $validated['password']]);

        return redirect()->route('admin.settings.index', ['tab' => 'password'])->with('status', 'Password changed.');
    }
    public function flushSeoCache(): RedirectResponse
    {
        $this->flushSeoCacheKeys();

        return redirect()->route('admin.settings.index', ['tab' => 'seo'])->with('status', 'SEO cache flushed.');
    }

    /**
     * A WebP copy of the logo for the header and footer. The original stays for
     * the favicon and the Apple touch icon, which need PNG for reliable support.
     * SVG logos are left as they are, and so is any file that fails to convert.
     */
    private function saveWebLogo(string $path, string $group): void
    {
        $disk = Storage::disk('public');

        if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'svg') {
            Setting::set('site_logo_web', null, $group);

            return;
        }

        $webPath = preg_replace('/\.[^.]+$/', '', $path).'-web.webp';

        try {
            Image::load($disk->path($path))
                ->format('webp')
                ->quality(85)
                ->save($disk->path($webPath));

            Setting::set('site_logo_web', Storage::url($webPath), $group);
        } catch (\Throwable $e) {
            Log::warning('Logo WebP conversion failed; the original is used.', ['error' => $e->getMessage()]);
            Setting::set('site_logo_web', null, $group);
        }
    }

    private function flushSeoCacheKeys(): void
    {
        Cache::forget('seo_sitemap_xml');
        Cache::forget('llms_txt_cache');
        Cache::forget('llms_txt_full_cache');
    }
}
