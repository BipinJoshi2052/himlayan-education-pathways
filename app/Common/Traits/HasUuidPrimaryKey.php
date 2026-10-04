<?php

declare(strict_types=1);

namespace App\Common\Traits;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * Standard primary-key strategy for every model in this boilerplate: an
 * ordered UUIDv7 string key instead of an auto-incrementing integer.
 *
 * UUIDv7 (not v4) is used deliberately — it's timestamp-prefixed, so keys
 * generated close together sort close together, avoiding the random-insert
 * index fragmentation that plain UUIDv4 causes on large tables.
 *
 * See docs/architecture.md.
 */
trait HasUuidPrimaryKey
{
    use HasUuids;

    /**
     * Eloquent calls initialize{Trait} automatically for every trait used by
     * a model. Setting $incrementing/$keyType here (rather than declaring
     * them as trait properties) avoids a fatal "incompatible property"
     * conflict with Model's own declarations of the same properties.
     */
    public function initializeHasUuidPrimaryKey(): void
    {
        $this->incrementing = false;
        $this->keyType = 'string';
    }

    public function newUniqueId(): string
    {
        return (string) str()->uuid7();
    }
}
