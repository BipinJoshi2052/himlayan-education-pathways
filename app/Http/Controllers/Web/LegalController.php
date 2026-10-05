<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

final class LegalController extends Controller
{
    public function privacy(): View
    {
        return view('web.legal.privacy');
    }

    public function terms(): View
    {
        return view('web.legal.terms');
    }
}
