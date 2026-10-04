<?php

declare(strict_types=1);

namespace App\Models;

use App\Common\Traits\HandlesTrash;
use App\Common\Traits\HasUuidPrimaryKey;
use App\Enums\InquiryStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'phone', 'subject', 'message', 'status', 'ip_address', 'user_agent', 'notes', 'location'])]
class Inquiry extends Model
{
    use HandlesTrash, HasUuidPrimaryKey;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => InquiryStatus::class,
            'location' => 'array',
        ];
    }
}
