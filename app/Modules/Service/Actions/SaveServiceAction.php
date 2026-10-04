<?php

declare(strict_types=1);

namespace App\Modules\Service\Actions;

use App\Common\Helpers\SlugGenerator;
use App\Modules\Service\DTOs\ServiceData;
use App\Modules\Service\Models\Service;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

final readonly class SaveServiceAction
{
    public function handle(
        ServiceData $data,
        ?Service $service = null,
        ?UploadedFile $coverImage = null,
        ?UploadedFile $brochurePdf = null,
    ): Service {
        return DB::transaction(function () use ($data, $service, $coverImage, $brochurePdf) {
            $isNew = $service === null;
            $service ??= new Service;

            $service->title = $data->title;
            $service->slug = $data->slug ?: SlugGenerator::make(Service::class, $data->primaryTitle(), $service->id);
            $service->summary = $data->summary;
            $service->description = $data->description;
            $service->icon = $data->icon;
            $service->fee = $data->fee;
            $service->duration = $data->duration;
            $service->is_featured = $data->isFeatured;
            $service->is_active = $data->isActive;
            $service->sort_order = $data->sortOrder;
            $service->meta_title = $data->metaTitle;
            $service->meta_description = $data->metaDescription;
            $service->meta_keywords = $data->metaKeywords;

            if ($isNew) {
                $service->created_by = auth()->id();
            }

            $service->save();

            if ($coverImage !== null) {
                $service->addMedia($coverImage)->toMediaCollection('cover_image');
            }

            if ($brochurePdf !== null) {
                $service->addMedia($brochurePdf)->toMediaCollection('brochure_pdf');
            }

            return $service;
        });
    }
}
