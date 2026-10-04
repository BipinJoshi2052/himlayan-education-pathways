<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Seo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

final class RobotsController extends Controller
{
    public function index(): Response
    {
        if (! app()->isProduction()) {
            $body = "User-agent: *\nDisallow: /\n";
        } else {
            $body = implode("\n", [
                'User-agent: *',
                'Allow: /',
                'Disallow: /admin',
                'Disallow: /api',
                'Disallow: /storage/private',
                '',
                'Sitemap: '.url('/sitemap.xml'),
                '',
            ]);
        }

        return response($body, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
