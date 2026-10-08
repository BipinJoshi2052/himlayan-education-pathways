<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

final class LegalController extends Controller
{
    public function privacy(): View
    {
        return view('web.legal.privacy', [
            'seoTitle' => __('web.meta.privacy.title'),
            'seoDescription' => __('web.meta.privacy.description'),
        ]);
    }

    public function terms(): View
    {
        return view('web.legal.terms', [
            'seoTitle' => __('web.meta.terms.title'),
            'seoDescription' => __('web.meta.terms.description'),
        ]);
    }
}
