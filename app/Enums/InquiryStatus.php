<?php

declare(strict_types=1);

namespace App\Enums;

enum InquiryStatus: string
{
    case Unread = 'unread';
    case Read = 'read';
    case Replied = 'replied';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Unread => 'Unread',
            self::Read => 'Read',
            self::Replied => 'Replied',
            self::Archived => 'Archived',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Unread => 'bg-primary-subtle text-primary',
            self::Read => 'bg-secondary-subtle text-secondary',
            self::Replied => 'bg-success-subtle text-success',
            self::Archived => 'bg-warning-subtle text-warning',
        };
    }
}
