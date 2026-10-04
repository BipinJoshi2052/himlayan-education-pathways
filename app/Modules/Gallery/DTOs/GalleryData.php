<?php

declare(strict_types=1);

namespace App\Modules\Gallery\DTOs;

final readonly class GalleryData
{
    /**
     * @param  array<string, string>  $title
     * @param  array<string, string|null>  $description
     * @param  array<string, string|null>  $metaTitle
     * @param  array<string, string|null>  $metaDescription
     */
    public function __construct(
        public array $title,
        public ?string $slug,
        public array $description,
        public bool $isFeatured,
        public bool $isActive,
        public int $sortOrder,
        public array $metaTitle,
        public array $metaDescription,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? [],
            slug: $data['slug'] ?? null,
            description: $data['description'] ?? [],
            isFeatured: (bool) ($data['is_featured'] ?? false),
            isActive: (bool) ($data['is_active'] ?? false),
            sortOrder: (int) ($data['sort_order'] ?? 0),
            metaTitle: $data['meta_title'] ?? [],
            metaDescription: $data['meta_description'] ?? [],
        );
    }

    public function primaryTitle(): string
    {
        return $this->title['en'] ?? (reset($this->title) ?: '');
    }
}
