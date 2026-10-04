<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Common\Services\LocaleOptions;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies the visitor's chosen public-site locale (set via LocaleController,
 * stored in session) for the duration of the request. Session-only — no
 * locale-prefixed routing exists (see docs/public-website.md), so this
 * doesn't affect URLs, only which translation HasTranslations resolves.
 */
final class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale');

        if (is_string($locale) && array_key_exists($locale, LocaleOptions::forWeb())) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
