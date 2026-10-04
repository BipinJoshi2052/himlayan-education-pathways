<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Modules\Post\Models\Post;
use App\Modules\Service\Models\Service;
use Illuminate\View\View;

final class HomeController extends Controller
{
    public function index(): View
    {
        $sections = Section::active()
            ->whereIn('key', ['hero', 'stats', 'journey', 'about', 'popular-categories', 'why-choose-us', 'leadership', 'testimonials', 'career-cta'])
            ->where('page_slug', 'home')
            ->with(['items.media', 'media'])
            ->get()
            ->keyBy('key');

        return view('web.home', [
            'sections' => $sections,
            'services' => Service::active()->orderBy('sort_order')->get(),
            'posts' => Post::published()->orderBy('sort_order')->limit(3)->get(),
        ]);
    }
}
