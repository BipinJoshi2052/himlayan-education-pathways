<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Service\Models\Service;
use Illuminate\View\View;

final class CourseController extends Controller
{
    public function index(): View
    {
        return view('web.courses.index', [
            'services' => Service::active()->orderBy('sort_order')->get(),
            'seoTitle' => __('web.meta.courses_index.title'),
            'seoDescription' => __('web.meta.courses_index.description'),
        ]);
    }

    public function show(string $slug): View
    {
        $service = Service::active()->where('slug', $slug)->firstOrFail();

        return view('web.courses.show', [
            'service' => $service,
            'relatedServices' => Service::active()->where('id', '!=', $service->id)->orderBy('sort_order')->limit(3)->get(),
            'seoEntity' => $service,
        ]);
    }
}
