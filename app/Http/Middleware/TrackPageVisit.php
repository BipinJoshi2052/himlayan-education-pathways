<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\PageVisit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Records public page views. Location is not looked up here — that happens
 * on the admin Page Visits page, so visitors never wait on an external API.
 */
final class TrackPageVisit
{
    private const BOT_PATTERN = '/bot|crawl|spider|slurp|facebookexternalhit|preview|monitor/i';

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldRecord($request, $response)) {
            return $response;
        }

        PageVisit::create([
            'path' => '/'.ltrim($request->path(), '/'),
            'ip_address' => (string) $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 500),
            'referer' => mb_substr((string) $request->headers->get('referer'), 0, 500) ?: null,
            'visited_at' => now(),
        ]);

        return $response;
    }

    private function shouldRecord(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET') || ! $response->isSuccessful()) {
            return false;
        }

        // Admin pages and anyone signed in (the admin browsing the site)
        // are not visitors, so they are never recorded.
        if ($request->is('admin', 'admin/*') || $request->user() !== null) {
            return false;
        }

        // Infinite-scroll fetches re-request the list page with ?page=N;
        // count the visit once, from the page itself.
        if ($request->query->has('page')) {
            return false;
        }

        return ! preg_match(self::BOT_PATTERN, (string) $request->userAgent());
    }
}
