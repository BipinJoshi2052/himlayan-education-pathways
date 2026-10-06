<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * TikTok videos listed by the admin in Settings → Social, drawn with TikTok's
 * own embed. Each link is looked up through TikTok's public oEmbed service
 * (no login or token) and the result is cached for six hours. Links that
 * fail are skipped, so the page never breaks.
 */
final class TikTokFeed
{
    private const OEMBED_URL = 'https://www.tiktok.com/oembed';

    private const CACHE_HOURS = 6;

    // TikTok's embed script shows "overload-protect" when too many players
    // load on one page, so keep this low.
    private const MAX_VIDEOS = 4;

    /**
     * @return array<int, array{url: string, html: string}>
     */
    public static function videos(): array
    {
        $links = self::links();

        $videos = [];

        foreach ($links as $url) {
            $embed = self::embed($url);

            if ($embed !== null) {
                $videos[] = ['url' => $url, 'html' => $embed];
            }
        }

        return $videos;
    }

    /**
     * The saved links that are TikTok video addresses, one per line,
     * de-duplicated and capped.
     *
     * @return array<int, string>
     */
    public static function links(): array
    {
        $raw = (string) Setting::get('tiktok_video_links', '');

        return collect(preg_split('/\R/', $raw) ?: [])
            ->map(fn (string $line) => trim($line))
            ->filter(fn (string $line) => self::isVideoLink($line))
            ->unique()
            ->take(self::MAX_VIDEOS)
            ->values()
            ->all();
    }

    public static function isVideoLink(string $url): bool
    {
        return (bool) preg_match('#^https://(www\.|m\.)?tiktok\.com/@[^/\s]+/video/\d+#i', $url);
    }

    private static function embed(string $url): ?string
    {
        $key = 'tiktok_oembed_'.md5($url);

        $cached = Cache::get($key);

        if (is_string($cached)) {
            return $cached;
        }

        try {
            $response = Http::timeout(8)->get(self::OEMBED_URL, ['url' => $url]);
        } catch (\Throwable $e) {
            Log::warning('TikTok oEmbed request failed.', ['error' => $e->getMessage()]);

            return null;
        }

        if (! $response->successful() || ! is_string($response->json('html'))) {
            Log::warning('TikTok oEmbed returned no embed.', ['status' => $response->status()]);

            return null;
        }

        $html = (string) $response->json('html');

        Cache::put($key, $html, now()->addHours(self::CACHE_HOURS));

        return $html;
    }
}
