<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\View\View;

final class AboutController extends Controller
{
    public function index(): View
    {
        $sections = Section::active()
            ->whereIn('key', ['about-intro', 'about-mission-vision', 'about-approach', 'about-stats', 'career-cta'])
            ->where('page_slug', 'about')
            ->with(['items.media', 'media'])
            ->get()
            ->keyBy('key');

        return view('web.about', [
            'sections' => $sections,
            'seoTitle' => __('web.meta.about.title'),
            'seoDescription' => __('web.meta.about.description'),
        ]);
    }
}
