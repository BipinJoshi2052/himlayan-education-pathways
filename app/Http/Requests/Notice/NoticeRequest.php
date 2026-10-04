<?php

declare(strict_types=1);

namespace App\Http\Requests\Notice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

final class NoticeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title.*' => ['nullable', 'string', 'max:255'],
            'message.*' => ['nullable', 'string', 'max:2000'],
            'link_url' => ['nullable', 'string', 'max:2048'],
            'link_text' => ['nullable', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'sort_order' => ['nullable', 'integer'],
            'image' => ['nullable', 'image', 'max:5120'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        // A notice has to show *something* — text, an image, or both — but
        // not nothing. An existing image (on edit, when no new file is
        // uploaded) still counts, so this only blocks the truly-empty case.
        $validator->after(function (Validator $validator) {
            $hasMessage = collect($this->input('message', []))->filter(fn ($v) => filled($v))->isNotEmpty();
            $hasNewImage = $this->hasFile('image');
            $existingNotice = $this->route('notice');
            $hasExistingImage = $existingNotice && $existingNotice->getFirstMediaUrl('image') !== '';

            if (! $hasMessage && ! $hasNewImage && ! $hasExistingImage) {
                $validator->errors()->add('message', 'A notice needs a message, an image, or both.');
            }
        });
    }
}
