<?php

declare(strict_types=1);

namespace App\Modules\Post\DTOs;

use App\Enums\PostStatus;
use App\Enums\PostType;

final readonly class PostData
{
    /**
     * title/summary/content/meta_* are locale => value arrays (e.g.
     * ['en' => 'Hello', 'ne' => '...']), assigned directly to the
     * translatable attribute so Spatie stores every locale at once —
     * see docs/posts-module.md for why this isn't a single string.
     *
     * @param  array<string, string>  $title
     * @param  array<string, string|null>  $summary
     * @param  array<string, string>  $content
     * @param  array<string, string|null>  $metaTitle
     * @param  array<string, string|null>  $metaDescription
     * @param  array<string, string|null>  $metaKeywords
     */
    public function __construct(
        public PostType $type,
        public array $title,
        public ?string $slug,
        public array $summary,
        public array $content,
        public bool $isFeatured,
        public PostStatus $status,
        public ?string $publishedAt,
        public int $sortOrder,
        public array $metaTitle,
        public array $metaDescription,
        public array $metaKeywords,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            type: PostType::from($data['type']),
            title: $data['title'] ?? [],
            slug: $data['slug'] ?? null,
            summary: $data['summary'] ?? [],
            content: $data['content'] ?? [],
            isFeatured: (bool) ($data['is_featured'] ?? false),
            status: PostStatus::from($data['status']),
            publishedAt: $data['published_at'] ?? null,
            sortOrder: (int) ($data['sort_order'] ?? 0),
            metaTitle: $data['meta_title'] ?? [],
            metaDescription: $data['meta_description'] ?? [],
            metaKeywords: $data['meta_keywords'] ?? [],
        );
    }

    /**
     * The primary-locale title, used to auto-generate a slug when none was
     * typed — falls back to the first available locale if 'en' is empty.
     */
    public function primaryTitle(): string
    {
        return $this->title['en'] ?? (reset($this->title) ?: '');
    }
}
