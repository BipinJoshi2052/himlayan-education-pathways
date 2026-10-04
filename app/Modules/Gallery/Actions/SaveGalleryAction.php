<?php

declare(strict_types=1);

namespace App\Modules\Gallery\Actions;

use App\Common\Helpers\SlugGenerator;
use App\Modules\Gallery\DTOs\GalleryData;
use App\Modules\Gallery\Models\Gallery;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

final readonly class SaveGalleryAction
{
    public function handle(GalleryData $data, ?Gallery $gallery = null, ?UploadedFile $cover = null): Gallery
    {
        return DB::transaction(function () use ($data, $gallery, $cover) {
            $isNew = $gallery === null;
            $gallery ??= new Gallery;

            $gallery->title = $data->title;
            $gallery->slug = $data->slug ?: SlugGenerator::make(Gallery::class, $data->primaryTitle(), $gallery->id);
            $gallery->description = $data->description;
            $gallery->is_featured = $data->isFeatured;
            $gallery->is_active = $data->isActive;
            $gallery->sort_order = $data->sortOrder;
            $gallery->meta_title = $data->metaTitle;
            $gallery->meta_description = $data->metaDescription;

            if ($isNew) {
                $gallery->created_by = auth()->id();
            }

            $gallery->save();

            if ($cover !== null) {
                $gallery->addMedia($cover)->toMediaCollection('cover');
            }

            return $gallery;
        });
    }
}
