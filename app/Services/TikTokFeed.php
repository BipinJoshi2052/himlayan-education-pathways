<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * TikTok videos listed by the admin in Settings → Social. The page shows a
 * thumbnail card per video; the TikTok player loads only when a visitor opens
 * the video in a popup. Thumbnails and titles come from TikTok's public oEmbed
 * service, asked for on the server and cached for six hours. Players never
 * start on page load, which avoids TikTok's "overload-protect" message.
 */
final class TikTokFeed
{
    private const OEMBED_URL = 'https://www.tiktok.com/oembed';

    private const CACHE_HOURS = 6;

    private const MAX_VIDEOS = 4;

    /**
     * @return array<int, array{url: string, id: string, title: string, author: string, thumbnail: ?string}>
     */
    public static function videos(): array
    {
        return collect(self::links())
            ->map(fn (string $url) => self::details($url))
            ->filter()
            ->values()
            ->all();
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

    /**
     * The numeric video ID from a link such as https://www.tiktok.com/@name/video/123.
     */
    public static function videoId(string $url): ?string
    {
        return preg_match('#/video/(\d+)#', $url, $m) ? $m[1] : null;
    }

    /**
     * @return array{url: string, id: string, title: string, author: string, thumbnail: ?string}|null
     */
    private static function details(string $url): ?array
    {
        $id = self::videoId($url);

        if ($id === null) {
            return null;
        }

        $info = self::oembed($url);

        return [
            'url' => $url,
            'id' => $id,
            'title' => (string) ($info['title'] ?? 'TikTok video'),
            'author' => (string) ($info['author_name'] ?? ''),
            'thumbnail' => isset($info['thumbnail_url']) ? (string) $info['thumbnail_url'] : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function oembed(string $url): array
    {
        $key = 'tiktok_oembed_'.md5($url);

        $cached = Cache::get($key);

        if (is_array($cached)) {
            return $cached;
        }

        try {
            $response = Http::timeout(8)->get(self::OEMBED_URL, ['url' => $url]);
        } catch (\Throwable $e) {
            Log::warning('TikTok oEmbed request failed.', ['error' => $e->getMessage()]);

            return [];
        }

        if (! $response->successful()) {
            Log::warning('TikTok oEmbed returned an error.', ['status' => $response->status()]);

            return [];
        }

        $info = (array) $response->json();

        Cache::put($key, $info, now()->addHours(self::CACHE_HOURS));

        return $info;
    }
}
