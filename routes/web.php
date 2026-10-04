<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReorderController;
use App\Http\Controllers\Admin\RichTextController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Web\AboutController;
use App\Http\Controllers\Web\BlogController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Web\CourseController;
use App\Http\Controllers\Web\FaqController;
use App\Http\Controllers\Web\GalleryController as PublicGalleryController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\LocaleController;
use App\Http\Controllers\Web\Seo\LlmsTxtController;
use App\Http\Controllers\Web\Seo\RobotsController;
use App\Http\Controllers\Web\Seo\SitemapController;
use Illuminate\Support\Facades\Route;

Route::middleware(\App\Http\Middleware\SetLocale::class)->name('web.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/about', [AboutController::class, 'index'])->name('about');
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{slug}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/faq', [FaqController::class, 'index'])->name('faq');
    Route::get('/gallery', [PublicGalleryController::class, 'index'])->name('gallery.index');
    Route::get('/gallery/{slug}', [PublicGalleryController::class, 'show'])->name('gallery.show');
    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
    Route::get('/contact', [ContactController::class, 'show'])->name('contact');
    Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');
});

Route::post('/contact/send', [ContactController::class, 'send'])
    ->middleware('throttle:5,1')
    ->name('contact.send');

Route::get('/robots.txt', [RobotsController::class, 'index'])->name('robots');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/llms.txt', [LlmsTxtController::class, 'summary'])->name('llms.summary');
Route::get('/llms-full.txt', [LlmsTxtController::class, 'full'])->name('llms.full');

// Named 'login'/'logout' (not 'admin.login') so Laravel's default auth
// middleware — which redirects unauthenticated users via the hardcoded
// route('login') — keeps working without overriding it. Only the URI moves
// under /admin; the whole admin portal lives there, including its own
// sign-in gateway, per "all backend portal routes should have /admin".
Route::prefix('admin')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])
        ->middleware('guest')
        ->name('login');

    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('guest');

    Route::post('/logout', [LoginController::class, 'destroy'])
        ->middleware('auth')
        ->name('logout');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/settings/password', [SettingController::class, 'updatePassword'])->name('settings.password.update');

    // Shared by every module's rich-text editor (Posts, Services, Sections,
    // Notices, ...) — gated by being an authenticated admin, not a specific
    // module permission, since the form itself already enforces that.
    Route::post('/rich-text/images', [RichTextController::class, 'uploadImage'])->name('rich-text.upload-image');

    Route::middleware('permission:manage-roles')->group(function () {
        Route::resource('roles', RoleController::class)->except('show');
    });

    Route::middleware('permission:manage-users')->group(function () {
        Route::resource('users', UserController::class)->except('show');
    });

    Route::middleware('permission:manage-sections')->group(function () {
        Route::resource('sections', SectionController::class)->except('show');
        Route::post('sections/{id}/restore', [SectionController::class, 'restore'])->name('sections.restore');
        Route::delete('sections/{id}/force-delete', [SectionController::class, 'forceDelete'])->name('sections.forceDelete');
    });

    Route::middleware('permission:manage-posts')->group(function () {
        Route::resource('posts', PostController::class)->except('show');
        Route::post('posts/{id}/restore', [PostController::class, 'restore'])->name('posts.restore');
        Route::delete('posts/{id}/force-delete', [PostController::class, 'forceDelete'])->name('posts.forceDelete');
    });

    Route::middleware('permission:manage-notices')->group(function () {
        Route::resource('notices', NoticeController::class)->except('show');
        Route::post('notices/{id}/restore', [NoticeController::class, 'restore'])->name('notices.restore');
        Route::delete('notices/{id}/force-delete', [NoticeController::class, 'forceDelete'])->name('notices.forceDelete');
    });

    Route::middleware('permission:manage-services')->group(function () {
        Route::resource('services', ServiceController::class)->except('show');
        Route::post('services/{id}/restore', [ServiceController::class, 'restore'])->name('services.restore');
        Route::delete('services/{id}/force-delete', [ServiceController::class, 'forceDelete'])->name('services.forceDelete');
    });

    Route::middleware('permission:manage-galleries')->group(function () {
        Route::resource('galleries', GalleryController::class)->except('show');
        Route::post('galleries/{id}/restore', [GalleryController::class, 'restore'])->name('galleries.restore');
        Route::delete('galleries/{id}/force-delete', [GalleryController::class, 'forceDelete'])->name('galleries.forceDelete');

        Route::post('galleries/{gallery}/upload', [GalleryController::class, 'uploadMedia'])->name('galleries.upload');
        Route::post('galleries/{gallery}/media-reorder', [GalleryController::class, 'reorderMedia'])->name('galleries.media-reorder');
        Route::post('galleries/media/{mediaId}/caption', [GalleryController::class, 'updateMediaCaption'])->name('galleries.media-caption');
        Route::delete('galleries/media/{mediaId}', [GalleryController::class, 'deleteMedia'])->name('galleries.media-delete');
    });

    Route::middleware('permission:manage-settings')->group(function () {
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('settings/flush-seo-cache', [SettingController::class, 'flushSeoCache'])->name('settings.flush-seo-cache');
    });

    Route::middleware('permission:manage-inquiries')->group(function () {
        Route::get('inquiries', [InquiryController::class, 'index'])->name('inquiries.index');
        Route::get('inquiries/{inquiry}', [InquiryController::class, 'show'])->name('inquiries.show');
        Route::patch('inquiries/{inquiry}/notes', [InquiryController::class, 'updateNotes'])->name('inquiries.notes');
        Route::delete('inquiries/{inquiry}', [InquiryController::class, 'destroy'])->name('inquiries.destroy');
        Route::post('inquiries/{id}/restore', [InquiryController::class, 'restore'])->name('inquiries.restore');
        Route::delete('inquiries/{id}/force-delete', [InquiryController::class, 'forceDelete'])->name('inquiries.forceDelete');
    });
});

// Registered in web.php (not api.php) so these have session/CSRF/auth
// available — routes/api.php has no session middleware without Sanctum.
// Still reachable at these literal "api/..." paths and still go through the
// ApiResponse envelope, since that routing is path-based. See docs/api-responses.md.
Route::post('api/admin/profile/theme-toggle', [ProfileController::class, 'toggleTheme'])
    ->middleware('auth')
    ->name('admin.profile.theme-toggle');

Route::post('api/admin/reorder/{model}', ReorderController::class)
    ->middleware('auth')
    ->name('admin.reorder');
