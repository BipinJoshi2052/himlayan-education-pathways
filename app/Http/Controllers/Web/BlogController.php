<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Post\Models\Post;
use Illuminate\View\View;

final class BlogController extends Controller
{
    public function index(): View
    {
        return view('web.blog.index', [
            'posts' => Post::published()->orderBy('sort_order')->paginate(9),
        ]);
    }

    public function show(string $slug): View
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();

        return view('web.blog.show', [
            'post' => $post,
            'recentPosts' => Post::published()->where('id', '!=', $post->id)->orderBy('sort_order')->limit(5)->get(),
            'seoEntity' => $post,
        ]);
    }
}
