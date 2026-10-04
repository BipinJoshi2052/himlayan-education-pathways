<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\InquiryStatus;
use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Modules\Post\Models\Post;
use App\Modules\Service\Models\Service;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'activeCoursesCount' => Service::active()->count(),
            'publishedPostsCount' => Post::published()->count(),
            'unreadInquiriesCount' => Inquiry::where('status', InquiryStatus::Unread)->count(),
        ]);
    }
}
