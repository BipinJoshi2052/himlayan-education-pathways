<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Gallery\Models\Gallery;
use Illuminate\View\View;

final class GalleryController extends Controller
{
    public function index(): View
    {
        return view('web.gallery.index', [
            'galleries' => Gallery::active()->with('media')->orderBy('sort_order')->get(),
        ]);
    }

    public function show(string $slug): View
    {
        $gallery = Gallery::active()->where('slug', $slug)->firstOrFail();

        return view('web.gallery.show', [
            'gallery' => $gallery,
            'photos' => $gallery->getMedia('photos'),
            'seoEntity' => $gallery,
        ]);
    }
}
