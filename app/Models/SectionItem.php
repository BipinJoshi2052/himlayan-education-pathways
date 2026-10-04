<?php

declare(strict_types=1);

namespace App\Models;

use App\Common\Traits\HasUuidPrimaryKey;
use Database\Factories\SectionItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

#[Fillable(['section_id', 'title', 'description', 'icon_or_badge', 'link_url', 'sort_order'])]
class SectionItem extends Model implements HasMedia
{
    /** @use HasFactory<SectionItemFactory> */
    use HasFactory, HasTranslations, HasUuidPrimaryKey, InteractsWithMedia;

    /**
     * @var array<int, string>
     */
    public array $translatable = ['title', 'description'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Section, $this>
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }
}
