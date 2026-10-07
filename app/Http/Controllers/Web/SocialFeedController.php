<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\FacebookFeed;
use App\Services\TikTokFeed;
use Illuminate\Http\Response;

/**
 * HTML fragments for the Facebook and TikTok sections of the About page. The
 * page fetches them after it has loaded, so visitors don't wait on Facebook or
 * TikTok while the page is being built. Empty output means nothing to show,
 * and the page keeps that section hidden.
 */
final class SocialFeedController extends Controller
{
    public function facebook(): Response
    {
        $posts = FacebookFeed::posts(6);

        $html = $posts === []
            ? ''
            : view('components.web.facebook-feed', [
                'posts' => $posts,
                'stats' => FacebookFeed::pageStats(),
                'pageUrl' => Setting::get('social_facebook_url'),
                'pageName' => Setting::get('site_name', config('app.name')),
                'pageLogo' => Setting::get('site_logo'),
            ])->render();

        return $this->fragment($html);
    }

    public function tiktok(): Response
    {
        $videos = TikTokFeed::videos();

        $html = $videos === []
            ? ''
            : view('components.web.tiktok-feed', [
                'videos' => $videos,
                'pageUrl' => Setting::get('social_tiktok_url'),
            ])->render();

        return $this->fragment($html);
    }

    private function fragment(string $html): Response
    {
        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Cache-Control' => 'public, max-age=900',
        ]);
    }
}
