<?php

declare(strict_types=1);

namespace App\Enums;

enum SectionLayoutType: string
{
    case SingleBlock = 'single_block';
    case Repeater = 'repeater';
    case Cards = 'cards';

    public function label(): string
    {
        return match ($this) {
            self::SingleBlock => 'Single Block',
            self::Repeater => 'Repeater',
            self::Cards => 'Cards',
        };
    }

    /**
     * Whether this layout type manages a list of App\Models\SectionItem rows.
     */
    public function usesItems(): bool
    {
        return $this !== self::SingleBlock;
    }
}
