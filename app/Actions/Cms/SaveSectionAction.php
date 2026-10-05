<?php

declare(strict_types=1);

namespace App\Actions\Cms;

use App\DTOs\Cms\SectionData;
use App\DTOs\Cms\SectionItemData;
use App\Models\Section;
use App\Models\SectionItem;
use Illuminate\Http\UploadedFile;

final readonly class SaveSectionAction
{
    /**
     * @param  array<int, UploadedFile>  $itemImages  keyed the same as
     *                                                  $data->items (form index)
     * @param  bool  $removeImage  clear the section's current image (no new file chosen)
     * @param  array<int, int>  $itemRemovals  form indexes whose current image is removed
     */
    public function handle(SectionData $data, ?Section $section = null, ?UploadedFile $image = null, array $itemImages = [], bool $removeImage = false, array $itemRemovals = []): Section
    {
        $isNew = $section === null;
        $section ??= new Section;

        $section->fill([
            'page_slug' => $data->pageSlug,
            'key' => $data->key,
            'layout_type' => $data->layoutType,
            'title' => $data->title,
            'subtitle' => $data->subtitle,
            'content' => $data->content,
            'settings' => $data->settings,
            'is_active' => $data->isActive,
            'sort_order' => $data->sortOrder,
        ]);

        if ($isNew) {
            $section->created_by = auth()->id();
        }

        $section->save();

        if ($image !== null) {
            $section->addMedia($image)->toMediaCollection('image');
        } elseif ($removeImage) {
            $section->clearMediaCollection('image');
        }

        $this->syncItems($section, $data->items, $itemImages, $itemRemovals);

        return $section->fresh('items');
    }

    /**
     * @param  array<int, SectionItemData>  $items
     * @param  array<int, UploadedFile>  $itemImages
     */
    private function syncItems(Section $section, array $items, array $itemImages, array $itemRemovals = []): void
    {
        $keptIds = [];

        foreach ($items as $index => $itemData) {
            $item = $itemData->id
                ? $section->items()->whereKey($itemData->id)->first()
                : null;

            $item ??= new SectionItem(['section_id' => $section->id]);

            $item->fill([
                'title' => $itemData->title,
                'description' => $itemData->description,
                'icon_or_badge' => $itemData->iconOrBadge,
                'link_url' => $itemData->linkUrl,
                'sort_order' => $itemData->sortOrder,
            ]);
            $item->save();

            if (isset($itemImages[$index])) {
                $item->addMedia($itemImages[$index])->toMediaCollection('image');
            } elseif (in_array($index, $itemRemovals, true)) {
                $item->clearMediaCollection('image');
            }

            $keptIds[] = $item->id;
        }

        $section->items()->whereNotIn('id', $keptIds)->delete();
    }
}
