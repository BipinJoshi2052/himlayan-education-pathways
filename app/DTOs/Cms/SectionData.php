<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

use App\Enums\SectionLayoutType;

final readonly class SectionData
{
    /**
     * title/subtitle/content are locale => value arrays (e.g. ['en' =>
     * 'Hello', 'ne' => '...']), assigned directly to the translatable
     * attribute so Spatie stores every locale at once — same pattern as
     * PostData, see docs/posts-module.md.
     *
     * @param  array<string, string>  $title
     * @param  array<string, string|null>  $subtitle
     * @param  array<string, string|null>  $content
     * @param  array<int, SectionItemData>  $items
     */
    public function __construct(
        public string $pageSlug,
        public string $key,
        public SectionLayoutType $layoutType,
        public array $title,
        public array $subtitle,
        public array $content,
        public ?array $settings,
        public bool $isActive,
        public int $sortOrder,
        public array $items,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            pageSlug: $data['page_slug'],
            key: $data['key'],
            layoutType: SectionLayoutType::from($data['layout_type']),
            title: $data['title'] ?? [],
            subtitle: $data['subtitle'] ?? [],
            content: $data['content'] ?? [],
            settings: $data['settings'] ?? null,
            isActive: (bool) ($data['is_active'] ?? false),
            sortOrder: (int) ($data['sort_order'] ?? 0),
            items: array_map(
                fn (array $item) => SectionItemData::fromArray($item),
                $data['items'] ?? [],
            ),
        );
    }
}
