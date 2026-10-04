<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Common\Services\LocaleOptions;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class LocaleController extends Controller
{
    public function switch(Request $request, string $locale): RedirectResponse
    {
        if (array_key_exists($locale, LocaleOptions::forWeb())) {
            $request->session()->put('locale', $locale);
        }

        // The Referer header is attacker-controllable — only honor it as a
        // redirect target if it actually points back at this app, otherwise
        // this would be an open redirect.
        $referer = $request->headers->get('referer');
        $redirectTo = ($referer && str_starts_with($referer, url('/'))) ? $referer : route('web.home');

        return redirect()->to($redirectTo);
    }
}
