<?php

declare(strict_types=1);

namespace App\Http\Requests\Post;

use App\Enums\PostStatus;
use App\Enums\PostType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

final class StorePostRequest extends FormRequest
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
            'type' => ['required', new Enum(PostType::class)],
            'title.en' => ['required', 'string', 'max:255'],
            'title.*' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('posts', 'slug')],
            'summary.*' => ['nullable', 'string', 'max:500'],
            'content.en' => ['required', 'string'],
            'content.*' => ['nullable', 'string'],
            'is_featured' => ['sometimes', 'boolean'],
            'status' => ['required', new Enum(PostStatus::class)],
            'published_at' => ['nullable', 'date'],
            'sort_order' => ['nullable', 'integer'],
            'featured_image' => ['nullable', 'image', 'max:5120'],
            'meta_title.*' => ['nullable', 'string', 'max:255'],
            'meta_description.*' => ['nullable', 'string', 'max:500'],
            'meta_keywords.*' => ['nullable', 'string', 'max:255'],
        ];
    }
}
