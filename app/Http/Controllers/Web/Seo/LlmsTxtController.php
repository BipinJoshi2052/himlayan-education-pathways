<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Seo;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Modules\Service\Models\Service;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

/**
 * /llms.txt (summary) and /llms-full.txt (expanded) — a convention for
 * letting LLM-based crawlers (ChatGPT, Claude, Perplexity, ...) read a
 * site's structure without having to parse full HTML pages.
 */
final class LlmsTxtController extends Controller
{
    public function summary(): Response
    {
        $body = Cache::remember('llms_txt_cache', now()->addHours(24), fn () => $this->build(full: false));

        return response($body, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    public function full(): Response
    {
        $body = Cache::remember('llms_txt_full_cache', now()->addHours(24), fn () => $this->build(full: true));

        return response($body, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    private function build(bool $full): string
    {
        $siteName = Setting::get('site_name', config('app.name'));
        $tagline = Setting::get('site_tagline');
        $contactEmail = Setting::get('contact_email');
        $contactPhone = Setting::get('contact_phone');

        $lines = ["# {$siteName}"];

        if ($tagline) {
            $lines[] = '';
            $lines[] = "> {$tagline}";
        }

        $lines[] = '';
        $lines[] = '## Contact';
        if ($contactEmail) {
            $lines[] = "- Email: {$contactEmail}";
        }
        if ($contactPhone) {
            $lines[] = "- Phone: {$contactPhone}";
        }
        if (! $contactEmail && ! $contactPhone) {
            $lines[] = '- (not configured — see Settings > General in the admin panel)';
        }

        $lines[] = '';
        $lines[] = '## Pages';
        $lines[] = '- [Home]('.route('web.home').')';
        $lines[] = '- [About]('.route('web.about').')';
        $lines[] = '- [Courses]('.route('web.courses.index').')';
        $lines[] = '- [FAQ]('.route('web.faq').')';
        $lines[] = '- [Blog]('.route('web.blog.index').')';
        $lines[] = '- [Contact]('.route('web.contact').')';

        $lines[] = '';
        $lines[] = '## Services & Courses';

        $services = Service::active()->orderBy('sort_order')->get();

        if ($services->isEmpty()) {
            $lines[] = '(none published yet)';
        }

        foreach ($services as $service) {
            $url = route('web.courses.show', $service->slug);

            if ($full) {
                $lines[] = "### {$service->title}";
                $lines[] = "- URL: {$url}";
                if ($service->duration) {
                    $lines[] = "- Duration: {$service->duration}";
                }
                if ($service->fee !== null) {
                    $lines[] = "- Fee: {$service->fee}";
                }
                $lines[] = '';
                $lines[] = (string) $service->summary;
                $lines[] = '';
            } else {
                $lines[] = "- [{$service->title}]({$url}): ".(string) $service->summary;
            }
        }

        return implode("\n", $lines)."\n";
    }
}
