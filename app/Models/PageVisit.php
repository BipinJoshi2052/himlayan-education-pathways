<?php

declare(strict_types=1);

namespace App\Models;

use App\Common\Traits\HasUuidPrimaryKey;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['path', 'ip_address', 'user_agent', 'referer', 'location', 'visited_at'])]
class PageVisit extends Model
{
    use HasUuidPrimaryKey;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'location' => 'array',
            'visited_at' => 'datetime',
        ];
    }
}
