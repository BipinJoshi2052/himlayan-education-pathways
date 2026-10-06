<?php

declare(strict_types=1);

namespace App\Modules\Service\Models;

use App\Common\Traits\HandlesTrash;
use App\Common\Traits\HasSeoMeta;
use App\Common\Traits\HasUuidPrimaryKey;
use App\Events\ServiceSaved;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

#[Fillable([
    'title', 'slug', 'summary', 'description', 'icon', 'fee', 'duration',
    'is_featured', 'is_active', 'sort_order', 'meta_title', 'meta_description', 'meta_keywords',
])]
class Service extends Model implements HasMedia
{
    use HandlesTrash, HasSeoMeta, HasTranslations, HasUuidPrimaryKey, InteractsWithMedia;

    /**
     * @var array<int, string>
     */
    public array $translatable = ['title', 'summary', 'description', 'meta_title', 'meta_description', 'meta_keywords'];

    /**
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'saved' => ServiceSaved::class,
        'deleted' => ServiceSaved::class,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fee' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover_image')->singleFile();
        $this->addMediaCollection('brochure_pdf')->singleFile();
    }

    /** Web-sized WebP copy of the cover for the public site. */
    public function registerMediaConversions(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('web')
            ->fit(\Spatie\Image\Enums\Fit::Max, 1200, 1200)
            ->format('webp')
            ->quality(80)
            ->performOnCollections('cover_image');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @param  Builder<Service>  $query
     * @return Builder<Service>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
