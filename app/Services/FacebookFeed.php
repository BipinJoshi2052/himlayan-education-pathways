<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Latest posts from the institute's Facebook Page, via the Graph API.
 *
 * Needs the Page ID and a Page access token (Settings → Social). The posts
 * are cached for an hour so visitors don't call Facebook on every page view.
 * If anything fails, this returns an empty list and the section stays hidden.
 */
final class FacebookFeed
{
    private const API_VERSION = 'v26.0';

    private const CACHE_KEY = 'facebook_feed_posts';

    private const STATS_CACHE_KEY = 'facebook_page_stats';

    private const CACHE_MINUTES = 60;

    /**
     * @return array<int, array{message: string, date: string, image: ?string, url: ?string}>
     */
    public static function posts(int $limit = 6): array
    {
        $cached = Cache::get(self::CACHE_KEY);

        if (is_array($cached)) {
            return $cached;
        }

        $pageId = trim((string) Setting::get('facebook_page_id', ''));
        $token = self::token();

        if ($pageId === '' || $token === null) {
            return [];
        }

        try {
            $response = Http::timeout(8)
                ->get('https://graph.facebook.com/'.self::API_VERSION."/{$pageId}/posts", [
                    'fields' => 'message,created_time,permalink_url,full_picture,likes.summary(true).limit(0),comments.summary(true).limit(0)',
                    'limit' => $limit,
                    'access_token' => $token,
                ]);
        } catch (\Throwable $e) {
            Log::warning('Facebook feed request failed.', ['error' => $e->getMessage()]);

            return [];
        }

        if (! $response->successful()) {
            // Facebook's error message and code say what is wrong (for example,
            // a missing permission or a wrong Page ID). The token is never logged.
            Log::warning('Facebook feed returned an error.', [
                'status' => $response->status(),
                'code' => $response->json('error.code'),
                'message' => $response->json('error.message'),
            ]);

            return [];
        }

        $posts = collect($response->json('data', []))
            ->filter(fn ($post) => filled($post['message'] ?? null) || filled($post['full_picture'] ?? null))
            ->map(function (array $post) {
                $url = $post['permalink_url'] ?? null;

                return [
                    'message' => Str::limit((string) ($post['message'] ?? ''), 220),
                    'date' => isset($post['created_time']) ? date('M d, Y', strtotime($post['created_time'])) : '',
                    'image' => $post['full_picture'] ?? null,
                    'url' => $url,
                    'likes' => $post['likes']['summary']['total_count'] ?? null,
                    'comments' => $post['comments']['summary']['total_count'] ?? null,
                    // Facebook video and reel links can be played in the page with
                    // the video plugin; other posts link out as before.
                    'video' => is_string($url) && (str_contains($url, '/videos/') || str_contains($url, '/reel/')),
                ];
            })
            ->values()
            ->all();

        Cache::put(self::CACHE_KEY, $posts, now()->addMinutes(self::CACHE_MINUTES));

        return $posts;
    }

    /**
     * The Page's followers and likes. Null when the Page or token is missing
     * or Facebook returns an error, so the stats line is simply not shown.
     *
     * @return array{followers: int, likes: int}|null
     */
    public static function pageStats(): ?array
    {
        $cached = Cache::get(self::STATS_CACHE_KEY);

        if (is_array($cached)) {
            return $cached;
        }

        $pageId = trim((string) Setting::get('facebook_page_id', ''));
        $token = self::token();

        if ($pageId === '' || $token === null) {
            return null;
        }

        try {
            $response = Http::timeout(8)
                ->get('https://graph.facebook.com/'.self::API_VERSION."/{$pageId}", [
                    'fields' => 'fan_count,followers_count',
                    'access_token' => $token,
                ]);
        } catch (\Throwable $e) {
            Log::warning('Facebook page stats request failed.', ['error' => $e->getMessage()]);

            return null;
        }

        if (! $response->successful()) {
            Log::warning('Facebook page stats returned an error.', [
                'status' => $response->status(),
                'code' => $response->json('error.code'),
                'message' => $response->json('error.message'),
            ]);

            return null;
        }

        $stats = [
            'followers' => (int) $response->json('followers_count', 0),
            'likes' => (int) $response->json('fan_count', 0),
        ];

        Cache::put(self::STATS_CACHE_KEY, $stats, now()->addMinutes(self::CACHE_MINUTES));

        return $stats;
    }

    /**
     * The token is stored encrypted; returns null when none is saved or it
     * can't be decrypted (for example, after APP_KEY changed).
     */
    private static function token(): ?string
    {
        $stored = Setting::get('facebook_page_token');

        if (! is_string($stored) || $stored === '') {
            return null;
        }

        try {
            return Crypt::decryptString($stored);
        } catch (\Throwable) {
            Log::warning('Facebook Page token could not be decrypted; re-enter it in Settings → Social.');

            return null;
        }
    }
}
