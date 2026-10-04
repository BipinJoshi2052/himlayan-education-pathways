<?php

declare(strict_types=1);

namespace App\Common\Helpers;

use Illuminate\Support\Str;

final class SlugGenerator
{
    /**
     * @param  class-string  $modelClass  A model using HandlesTrash (so withTrashed() exists).
     * @param  string|null  $currentId  Exclude this record's own row when checking for a collision (updates).
     */
    public static function make(string $modelClass, string $title, ?string $currentId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $suffix = 1;

        while (
            $modelClass::withTrashed()
                ->where('slug', $slug)
                ->when($currentId, fn ($query) => $query->whereKeyNot($currentId))
                ->exists()
        ) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
