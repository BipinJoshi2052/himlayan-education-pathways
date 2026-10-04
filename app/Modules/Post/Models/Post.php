<?php

declare(strict_types=1);

namespace App\Modules\Post\Models;

use App\Common\Traits\HandlesTrash;
use App\Common\Traits\HasSeoMeta;
use App\Common\Traits\HasUuidPrimaryKey;
use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Events\PostSaved;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

#[Fillable([
    'type', 'title', 'slug', 'summary', 'content', 'is_featured', 'status', 'published_at', 'sort_order',
    'meta_title', 'meta_description', 'meta_keywords',
])]
class Post extends Model implements HasMedia
{
    use HandlesTrash, HasSeoMeta, HasTranslations, HasUuidPrimaryKey, InteractsWithMedia;

    /**
     * @var array<int, string>
     */
    public array $translatable = ['title', 'summary', 'content', 'meta_title', 'meta_description', 'meta_keywords'];

    /**
     * Drives sitemap/llms.txt cache invalidation (FlushSeoCache listener) —
     * see docs/seo-discovery.md.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'saved' => PostSaved::class,
        'deleted' => PostSaved::class,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => PostType::class,
            'status' => PostStatus::class,
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured_image')->singleFile();
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        // Fit::Crop forces the exact target dimensions (cropping to fill),
        // rather than Spatie's default of fitting-within while preserving
        // aspect ratio — "thumb 300x200" means exactly that, not "up to".
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 300, 200)
            ->format('webp')
            ->performOnCollections('featured_image');

        $this->addMediaConversion('medium')
            ->fit(Fit::Crop, 800, 500)
            ->format('webp')
            ->performOnCollections('featured_image');
    }

    /**
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PostStatus::Published)
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }
}
