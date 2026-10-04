<?php

declare(strict_types=1);

namespace App\Common\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Standard soft-delete behavior for every trashable content model:
 * - Frees up a unique `slug` immediately on soft delete (so a new record can
 *   reuse it) by suffixing it, and restores the clean slug on restore.
 * - Wipes Spatie Media Library files/conversions on a real (force) delete.
 * - Adds a `filterTrash` scope for the `?trash=only|with` admin list toggle.
 *
 * Use this INSTEAD OF Illuminate's own SoftDeletes trait, not alongside it —
 * it already includes SoftDeletes.
 */
trait HandlesTrash
{
    use SoftDeletes;

    public static function bootHandlesTrash(): void
    {
        static::deleted(function (Model $model): void {
            if ($model->isForceDeleting()) {
                return;
            }

            if (array_key_exists('slug', $model->getAttributes()) && filled($model->slug)) {
                $model->slug = $model->slug.'-deleted-'.now()->timestamp;
                $model->saveQuietly();
            }
        });

        static::restored(function (Model $model): void {
            if (array_key_exists('slug', $model->getAttributes()) && filled($model->slug)) {
                $base = preg_replace('/-deleted-\d+$/', '', (string) $model->slug);
                $model->slug = $model->generateUniqueTrashSlug($base);
                $model->saveQuietly();
            }
        });

        static::forceDeleting(function (Model $model): void {
            if (method_exists($model, 'clearMediaCollection')) {
                $model->media()->get()->each->delete();
            }
        });
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeFilterTrash(Builder $query, ?string $status): Builder
    {
        return match ($status) {
            'only' => $query->onlyTrashed(),
            'with' => $query->withTrashed(),
            default => $query,
        };
    }

    /**
     * Ensure $base doesn't collide with another record's slug (trashed ones
     * included, since their suffixed slugs could theoretically still clash).
     */
    protected function generateUniqueTrashSlug(string $base): string
    {
        $slug = $base;
        $suffix = 1;

        while (
            static::withTrashed()
                ->where('slug', $slug)
                ->whereKeyNot($this->getKey())
                ->exists()
        ) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
