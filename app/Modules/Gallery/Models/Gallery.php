<?php

declare(strict_types=1);

namespace App\Modules\Gallery\Models;

use App\Common\Traits\HandlesTrash;
use App\Common\Traits\HasSeoMeta;
use App\Common\Traits\HasUuidPrimaryKey;
use App\Events\GallerySaved;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

#[Fillable(['title', 'slug', 'description', 'is_featured', 'is_active', 'sort_order', 'meta_title', 'meta_description'])]
class Gallery extends Model implements HasMedia
{
    use HandlesTrash, HasSeoMeta, HasTranslations, HasUuidPrimaryKey, InteractsWithMedia;

    /**
     * @var array<int, string>
     */
    public array $translatable = ['title', 'description', 'meta_title', 'meta_description'];

    /**
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'saved' => GallerySaved::class,
        'deleted' => GallerySaved::class,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile();
        $this->addMediaCollection('photos');
    }

    /** Web-sized WebP copies of the cover and gallery photos for the public site. */
    public function registerMediaConversions(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('web')
            ->fit(\Spatie\Image\Enums\Fit::Max, 1200, 1200)
            ->format('webp')
            ->quality(80)
            ->performOnCollections('cover', 'photos');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @param  Builder<Gallery>  $query
     * @return Builder<Gallery>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
