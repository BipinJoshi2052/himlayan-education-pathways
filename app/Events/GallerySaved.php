<?php

declare(strict_types=1);

namespace App\Events;

use App\Modules\Gallery\Models\Gallery;

final class GallerySaved
{
    public function __construct(public readonly Gallery $gallery) {}
}
