<?php

declare(strict_types=1);

namespace App\DTOs\Notice;

final readonly class NoticeData
{
    /**
     * title/message are locale => value arrays, same pattern as
     * PostData/SectionData — assigned directly to the translatable
     * attribute so Spatie stores every locale at once.
     *
     * @param  array<string, string|null>  $title
     * @param  array<string, string|null>  $message
     */
    public function __construct(
        public array $title,
        public array $message,
        public ?string $linkUrl,
        public ?string $linkText,
        public string $displayMode,
        public bool $isActive,
        public ?string $startsAt,
        public ?string $endsAt,
        public int $sortOrder,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? [],
            message: $data['message'] ?? [],
            linkUrl: $data['link_url'] ?? null,
            linkText: $data['link_text'] ?? null,
            displayMode: $data['display_mode'] ?? 'once_forever',
            isActive: (bool) ($data['is_active'] ?? false),
            startsAt: $data['starts_at'] ?? null,
            endsAt: $data['ends_at'] ?? null,
            sortOrder: (int) ($data['sort_order'] ?? 0),
        );
    }
}
