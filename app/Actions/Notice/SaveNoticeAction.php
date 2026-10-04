<?php

declare(strict_types=1);

namespace App\Actions\Notice;

use App\DTOs\Notice\NoticeData;
use App\Models\Notice;
use Illuminate\Http\UploadedFile;

final readonly class SaveNoticeAction
{
    public function handle(NoticeData $data, ?Notice $notice = null, ?UploadedFile $image = null): Notice
    {
        $isNew = $notice === null;
        $notice ??= new Notice;

        $notice->fill([
            'title' => $data->title,
            'message' => $data->message,
            'link_url' => $data->linkUrl,
            'link_text' => $data->linkText,
            'is_active' => $data->isActive,
            'starts_at' => $data->startsAt,
            'ends_at' => $data->endsAt,
            'sort_order' => $data->sortOrder,
        ]);

        if ($isNew) {
            $notice->created_by = auth()->id();
        }

        $notice->save();

        if ($image !== null) {
            $notice->addMedia($image)->toMediaCollection('image');
        }

        return $notice;
    }
}
