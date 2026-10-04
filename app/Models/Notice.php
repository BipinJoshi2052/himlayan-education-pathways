<?php

declare(strict_types=1);

namespace App\Models;

use App\Common\Traits\HandlesTrash;
use App\Common\Traits\HasUuidPrimaryKey;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

/**
 * A site-wide popup notice — text, an image, or both, with an optional
 * link. Deliberately its own small model rather than another Section: a
 * Section is page-layout content, a Notice is a time-boxed, dismissible
 * announcement with its own active-window logic — see docs/admin-ui.md.
 */
#[Fillable(['title', 'message', 'link_url', 'link_text', 'display_mode', 'is_active', 'starts_at', 'ends_at', 'sort_order'])]
class Notice extends Model implements HasMedia
{
    use HandlesTrash, HasTranslations, HasUuidPrimaryKey, InteractsWithMedia;

    /**
     * @var array<int, string>
     */
    public array $translatable = ['title', 'message'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @param  Builder<Notice>  $query
     * @return Builder<Notice>
     */
    public function scopeCurrentlyActive(Builder $query): Builder
    {
        $now = Carbon::now();

        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now));
    }
}
