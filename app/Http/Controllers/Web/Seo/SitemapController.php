<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Seo;

use App\Http\Controllers\Controller;
use App\Modules\Gallery\Models\Gallery;
use App\Modules\Post\Models\Post;
use App\Modules\Service\Models\Service;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

final class SitemapController extends Controller
{
    public function index(): Response
    {
        // Spec said "Laravel's file cache" — this project deliberately uses
        // the `database` cache driver everywhere (see docs/database.md,
        // a Phase 1 architecture decision for low-resource hosting) and
        // nowhere else introduces a second cache backend, so this uses the
        // default (database) store rather than Cache::store('file') to stay
        // consistent with that decision.
        $xml = Cache::remember('seo_sitemap_xml', now()->addHours(6), fn () => view('seo.sitemap', [
            'urls' => $this->collectUrls(),
        ])->render());

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    /**
     * @return array<int, array{loc: string, lastmod: string, changefreq: string, priority: string}>
     */
    private function collectUrls(): array
    {
        $urls = [$this->entry(url('/'), now(), 'daily', '1.0')];

        // Posts and Services now have real public pages (routes web.blog.show
        // / web.courses.show — see docs/public-website.md), so these use
        // route() against the actual registered routes rather than a
        // hand-built guess at the URL. Galleries still have no public page,
        // so that one is still a predictable-but-hypothetical convention.
        Post::published()->get()->each(function (Post $post) use (&$urls) {
            $urls[] = $this->entry(route('web.blog.show', $post->slug), $post->updated_at, 'weekly', '0.7');
        });

        Service::active()->get()->each(function (Service $service) use (&$urls) {
            $urls[] = $this->entry(route('web.courses.show', $service->slug), $service->updated_at, 'monthly', '0.8');
        });

        Gallery::active()->get()->each(function (Gallery $gallery) use (&$urls) {
            $urls[] = $this->entry(route('web.gallery.show', $gallery->slug), $gallery->updated_at, 'monthly', '0.5');
        });

        return $urls;
    }

    /**
     * @return array{loc: string, lastmod: string, changefreq: string, priority: string}
     */
    private function entry(string $loc, \DateTimeInterface $lastmod, string $changefreq, string $priority): array
    {
        // No locale-prefixed routing exists (no /en/..., /ne/... URL
        // variants — see SetLocale middleware) — every language is served
        // from the same URL, so there's no second URL for hreflang to
        // point to. Per Google's own guidance, hreflang only makes sense
        // between genuinely distinct per-language URLs; a self-referencing
        // set (every locale pointing at the same URL) isn't valid and was
        // removed rather than shipped as a no-op. See docs/public-website.md.
        return [
            'loc' => $loc,
            'lastmod' => $lastmod instanceof Carbon ? $lastmod->toAtomString() : $lastmod->format(DATE_ATOM),
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }
}
