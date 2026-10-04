<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Common\Helpers\ApiResponse;
use App\Common\Services\LocaleOptions;
use App\Common\Traits\HasTrashActions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Gallery\GalleryRequest;
use App\Modules\Gallery\Actions\SaveGalleryAction;
use App\Modules\Gallery\DTOs\GalleryData;
use App\Modules\Gallery\Models\Gallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class GalleryController extends Controller
{
    use HasTrashActions;

    public function index(Request $request): View
    {
        $galleries = QueryBuilder::for(Gallery::class)
            ->filterTrash($request->query('trash'))
            ->allowedFilters(
                AllowedFilter::exact('is_active'),
                AllowedFilter::callback('search', fn ($query, $value) => $query->where('title->en', 'like', "%{$value}%")),
            )
            ->allowedSorts('sort_order', 'created_at')
            ->defaultSort('sort_order')
            ->withCount(['media as photos_count' => fn ($query) => $query->where('collection_name', 'photos')])
            ->paginate(20)
            ->withQueryString();

        return view('admin.galleries.index', [
            'galleries' => $galleries,
            'activeCount' => Gallery::count(),
            'trashedCount' => Gallery::onlyTrashed()->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.galleries.form', [
            'gallery' => new Gallery,
            'locales' => array_keys(LocaleOptions::forAdmin()),
        ]);
    }

    public function store(GalleryRequest $request, SaveGalleryAction $action): RedirectResponse
    {
        $gallery = $action->handle(GalleryData::fromArray($request->validated()), cover: $request->file('cover'));

        return redirect()->route('admin.galleries.edit', $gallery)->with('status', 'Gallery created — now add some photos.');
    }

    public function edit(Gallery $gallery): View
    {
        $gallery->load('media');

        return view('admin.galleries.form', [
            'gallery' => $gallery,
            'locales' => array_keys(LocaleOptions::forAdmin()),
        ]);
    }

    public function update(GalleryRequest $request, Gallery $gallery, SaveGalleryAction $action): RedirectResponse
    {
        $action->handle(GalleryData::fromArray($request->validated()), $gallery, $request->file('cover'));

        return redirect()->route('admin.galleries.edit', $gallery)->with('status', 'Gallery updated.');
    }

    public function destroy(Gallery $gallery): RedirectResponse
    {
        $gallery->delete();

        return redirect()->route('admin.galleries.index')->with('status', 'Gallery deleted.');
    }

    /**
     * POST /admin/galleries/{gallery}/upload — one Dropzone request per file.
     */
    public function uploadMedia(Request $request, Gallery $gallery): JsonResponse
    {
        $request->validate(['file' => ['required', 'image', 'max:8192']]);

        $media = $gallery->addMedia($request->file('file'))
            ->withCustomProperties(['caption' => '', 'alt_text' => ''])
            ->toMediaCollection('photos');

        return ApiResponse::success([
            'id' => $media->id,
            'url' => $media->getUrl(),
            'thumb_url' => $media->getUrl(),
        ], 'Uploaded.');
    }

    /**
     * POST /admin/galleries/media/{mediaId}/caption — not in the original
     * endpoint list, but "inline caption inputs" in the form view has to
     * persist somewhere; there's no other endpoint that could do it.
     */
    public function updateMediaCaption(Request $request, string $mediaId): JsonResponse
    {
        $validated = $request->validate([
            'caption' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ]);

        $media = Media::query()->where('model_type', Gallery::class)->findOrFail($mediaId);
        $media->setCustomProperty('caption', $validated['caption'] ?? '');
        $media->setCustomProperty('alt_text', $validated['alt_text'] ?? '');
        $media->save();

        return ApiResponse::success(null, 'Caption saved.');
    }

    /**
     * DELETE /admin/galleries/media/{mediaId}
     */
    public function deleteMedia(string $mediaId): JsonResponse
    {
        $media = Media::query()->where('model_type', Gallery::class)->findOrFail($mediaId);
        $media->delete();

        return ApiResponse::success(null, 'Photo deleted.');
    }

    /**
     * POST /admin/galleries/{gallery}/media-reorder
     */
    public function reorderMedia(Request $request, Gallery $gallery): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer'],
            'items.*.order' => ['required', 'integer'],
        ]);

        DB::transaction(function () use ($gallery, $validated) {
            foreach ($validated['items'] as $item) {
                Media::query()
                    ->where('model_type', Gallery::class)
                    ->where('model_id', $gallery->id)
                    ->whereKey($item['id'])
                    ->update(['order_column' => $item['order']]);
            }
        });

        return ApiResponse::success(null, 'Photo order updated.');
    }

    protected function trashModelClass(): string
    {
        return Gallery::class;
    }
}
