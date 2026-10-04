<?php

declare(strict_types=1);

namespace App\Enums;

enum PostType: string
{
    case Post = 'post';
    case News = 'news';

    public function label(): string
    {
        return match ($this) {
            self::Post => 'Post',
            self::News => 'News',
        };
    }
}
