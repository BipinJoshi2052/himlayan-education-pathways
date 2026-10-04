<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

final readonly class SectionItemData
{
    /**
     * title/description are locale => value arrays — see SectionData.
     *
     * @param  array<string, string>  $title
     * @param  array<string, string|null>  $description
     */
    public function __construct(
        public ?string $id,
        public array $title,
        public array $description,
        public ?string $iconOrBadge,
        public ?string $linkUrl,
        public int $sortOrder,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            title: $data['title'] ?? [],
            description: $data['description'] ?? [],
            iconOrBadge: $data['icon_or_badge'] ?? null,
            linkUrl: $data['link_url'] ?? null,
            sortOrder: (int) ($data['sort_order'] ?? 0),
        );
    }
}
