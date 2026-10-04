<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\GallerySaved;
use App\Events\PostSaved;
use App\Events\ServiceSaved;
use Illuminate\Support\Facades\Cache;

/**
 * No app/Providers/EventServiceProvider exists in this Laravel version —
 * events are auto-discovered from this handle() method's parameter type.
 * A union type registers this listener for all three event classes without
 * any manual $listen mapping.
 */
final class FlushSeoCache
{
    public function handle(PostSaved|ServiceSaved|GallerySaved $event): void
    {
        Cache::forget('seo_sitemap_xml');
        Cache::forget('llms_txt_cache');
        Cache::forget('llms_txt_full_cache');
    }
}
