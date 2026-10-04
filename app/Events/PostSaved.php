<?php

declare(strict_types=1);

namespace App\Events;

use App\Modules\Post\Models\Post;

final class PostSaved
{
    public function __construct(public readonly Post $post) {}
}
