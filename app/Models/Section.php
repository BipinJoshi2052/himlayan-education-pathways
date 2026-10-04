<?php

declare(strict_types=1);

namespace App\Models;

use App\Common\Traits\HandlesTrash;
use App\Common\Traits\HasUuidPrimaryKey;
use App\Enums\SectionLayoutType;
use Database\Factories\SectionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

#[Fillable(['page_slug', 'key', 'layout_type', 'title', 'subtitle', 'content', 'settings', 'is_active', 'sort_order'])]
class Section extends Model implements HasMedia
{
    /** @use HasFactory<SectionFactory> */
    use HandlesTrash, HasFactory, HasTranslations, HasUuidPrimaryKey, InteractsWithMedia;

    /**
     * @var array<int, string>
     */
    public array $translatable = ['title', 'subtitle', 'content'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'layout_type' => SectionLayoutType::class,
            'settings' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return HasMany<SectionItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(SectionItem::class)->orderBy('sort_order');
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
     * @param  Builder<Section>  $query
     * @return Builder<Section>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
