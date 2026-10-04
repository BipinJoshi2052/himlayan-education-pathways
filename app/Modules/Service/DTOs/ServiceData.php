<?php

declare(strict_types=1);

namespace App\Modules\Service\DTOs;

final readonly class ServiceData
{
    /**
     * @param  array<string, string>  $title
     * @param  array<string, string|null>  $summary
     * @param  array<string, string>  $description
     * @param  array<string, string|null>  $metaTitle
     * @param  array<string, string|null>  $metaDescription
     * @param  array<string, string|null>  $metaKeywords
     */
    public function __construct(
        public array $title,
        public ?string $slug,
        public array $summary,
        public array $description,
        public ?string $icon,
        public ?string $fee,
        public ?string $duration,
        public bool $isFeatured,
        public bool $isActive,
        public int $sortOrder,
        public array $metaTitle,
        public array $metaDescription,
        public array $metaKeywords,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? [],
            slug: $data['slug'] ?? null,
            summary: $data['summary'] ?? [],
            description: $data['description'] ?? [],
            icon: $data['icon'] ?? null,
            fee: $data['fee'] ?? null,
            duration: $data['duration'] ?? null,
            isFeatured: (bool) ($data['is_featured'] ?? false),
            isActive: (bool) ($data['is_active'] ?? false),
            sortOrder: (int) ($data['sort_order'] ?? 0),
            metaTitle: $data['meta_title'] ?? [],
            metaDescription: $data['meta_description'] ?? [],
            metaKeywords: $data['meta_keywords'] ?? [],
        );
    }

    public function primaryTitle(): string
    {
        return $this->title['en'] ?? (reset($this->title) ?: '');
    }
}
