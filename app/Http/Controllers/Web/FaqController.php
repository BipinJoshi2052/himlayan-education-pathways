<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\View\View;

final class FaqController extends Controller
{
    public function index(): View
    {
        $section = Section::active()
            ->where('page_slug', 'faq')
            ->where('key', 'faq-list')
            ->with('items')
            ->first();

        return view('web.faq', [
            'section' => $section,
            'seoTitle' => __('web.meta.faq.title'),
            'seoDescription' => __('web.meta.faq.description'),
        ]);
    }
}
