<?php

declare(strict_types=1);

namespace App\Common\Services;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;

final class SeoService
{
    /**
     * @return array{title: string, description: ?string, keywords: ?string, og_image: ?string, twitter_handle: ?string}
     */
    /**
     * $title/$description are for a page with no Eloquent record of its own
     * (About, FAQ, Contact, the listing pages, Privacy, Terms) — the only way
     * those pages had a distinct <title> before was the global Setting, so
     * every one of them showed the same text. They take precedence over the
     * entity and the global Setting, but not over the entity's own values
     * (a Post/Service's own meta_title/title still wins on its own page).
     */
    public static function resolve(?Model $entity = null, ?string $title = null, ?string $description = null): array
    {
        return [
            'title' => self::entityAttr($entity, 'meta_title')
                ?? self::entityAttr($entity, 'title')
                ?? $title
                ?? Setting::getTranslatable('seo_meta_title')
                ?? config('app.name'),

            'description' => self::entityAttr($entity, 'meta_description')
                ?? self::entityAttr($entity, 'summary')
                ?? $description
                ?? Setting::getTranslatable('seo_meta_description'),

            'keywords' => self::entityAttr($entity, 'meta_keywords')
                ?? Setting::getTranslatable('seo_meta_keywords'),

            'og_image' => self::absoluteUrl(
                self::firstMediaUrl($entity, 'featured_image')
                    ?? self::firstMediaUrl($entity, 'cover_image')
                    ?? Setting::get('seo_default_og_image')
                    ?? Setting::get('site_logo')
            ),

            'twitter_handle' => Setting::get('social_twitter_handle'),
        ];
    }

    /**
     * Model::shouldBeStrict() throws on accessing an attribute a model
     * doesn't have (e.g. Gallery has no meta_keywords) — same defensive
     * check as HasSeoMeta::seoAttr(), duplicated here because this resolver
     * must work for entities that don't use that trait at all (or no
     * entity/null, for a plain static page).
     */
    private static function entityAttr(?Model $entity, string $key): ?string
    {
        if ($entity === null || ! array_key_exists($key, $entity->getAttributes())) {
            return null;
        }

        $value = $entity->getAttribute($key);

        return filled($value) ? $value : null;
    }

    private static function firstMediaUrl(?Model $entity, string $collection): ?string
    {
        if ($entity === null || ! method_exists($entity, 'getFirstMediaUrl')) {
            return null;
        }

        $url = $entity->getFirstMediaUrl($collection);

        return filled($url) ? $url : null;
    }

    /**
     * og:image (and twitter:image) must be an absolute URL — Facebook and
     * most other scrapers ignore a relative one. Setting::get() for an
     * uploaded file returns a root-relative path (Storage::url()), e.g.
     * "/storage/settings/logo.png", so that needs the domain prepended;
     * Spatie's getFirstMediaUrl() is already absolute and passes through.
     */
    private static function absoluteUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        return str_starts_with($path, 'http://') || str_starts_with($path, 'https://')
            ? $path
            : url($path);
    }
}
