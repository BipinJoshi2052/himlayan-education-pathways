<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Common\Services\LocaleOptions;
use App\Common\Traits\HasTrashActions;
use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Modules\Post\Actions\CreatePostAction;
use App\Modules\Post\Actions\UpdatePostAction;
use App\Modules\Post\DTOs\PostData;
use App\Modules\Post\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class PostController extends Controller
{
    use HasTrashActions;

    public function index(Request $request): View
    {
        $posts = QueryBuilder::for(Post::class)
            ->filterTrash($request->query('trash'))
            ->allowedFilters(
                AllowedFilter::exact('type'),
                AllowedFilter::exact('status'),
                AllowedFilter::callback('search', function ($query, $value) {
                    $query->where(function ($q) use ($value) {
                        $q->where('slug', 'like', "%{$value}%")
                            ->orWhere('title->en', 'like', "%{$value}%");
                    });
                }),
            )
            ->allowedSorts('sort_order', 'published_at', 'created_at')
            ->defaultSort('sort_order')
            ->paginate(20)
            ->withQueryString();

        return view('admin.posts.index', [
            'posts' => $posts,
            'activeCount' => Post::count(),
            'trashedCount' => Post::onlyTrashed()->count(),
            'types' => PostType::cases(),
            'statuses' => PostStatus::cases(),
        ]);
    }

    public function create(): View
    {
        return view('admin.posts.form', [
            'post' => new Post,
            'types' => PostType::cases(),
            'statuses' => PostStatus::cases(),
            'locales' => array_keys(LocaleOptions::forAdmin()),
        ]);
    }

    public function store(StorePostRequest $request, CreatePostAction $action): RedirectResponse
    {
        $post = $action->handle(PostData::fromArray($request->validated()), $request->file('featured_image'));

        return redirect()->route('admin.posts.edit', $post)->with('status', 'Post created.');
    }

    public function edit(Post $post): View
    {
        return view('admin.posts.form', [
            'post' => $post,
            'types' => PostType::cases(),
            'statuses' => PostStatus::cases(),
            'locales' => array_keys(LocaleOptions::forAdmin()),
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post, UpdatePostAction $action): RedirectResponse
    {
        $action->handle($post, PostData::fromArray($request->validated()), $request->file('featured_image'));

        if ($request->boolean('remove_featured_image') && ! $request->hasFile('featured_image')) {
            $post->clearMediaCollection('featured_image');
        }

        return redirect()->route('admin.posts.edit', $post)->with('status', 'Post updated.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('admin.posts.index')->with('status', 'Post deleted.');
    }

    protected function trashModelClass(): string
    {
        return Post::class;
    }
}
