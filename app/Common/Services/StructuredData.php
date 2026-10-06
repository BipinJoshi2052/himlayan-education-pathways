<?php

declare(strict_types=1);

namespace App\Common\Services;

use App\Models\Setting;
use App\Modules\Post\Models\Post;
use App\Modules\Service\Models\Service;

/**
 * JSON-LD structured data for search engines. Everything comes from Settings
 * and the content records, so it stays in step with what admins edit.
 * Printed by components/web/json-ld.blade.php.
 */
final class StructuredData
{
    /**
     * The institute itself. Shown on every public page.
     *
     * @return array<string, mixed>
     */
    public static function organization(): array
    {
        $name = (string) Setting::get('site_name', config('app.name'));

        $sameAs = collect([
            'social_facebook_url', 'social_instagram_url', 'social_linkedin_url',
            'social_youtube_url', 'social_tiktok_url',
        ])
            ->map(fn (string $key) => Setting::get($key))
            ->filter(fn ($url) => is_string($url) && filter_var($url, FILTER_VALIDATE_URL))
            ->values()
            ->all();

        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'EducationalOrganization',
            '@id' => url('/').'#organization',
            'name' => $name,
            'url' => url('/'),
            'description' => (string) Setting::get('site_tagline', ''),
            'logo' => self::absolute(Setting::get('site_logo')),
            'email' => Setting::get('contact_email'),
            'telephone' => Setting::get('contact_phone'),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => (string) Setting::get('site_address', ''),
                'addressLocality' => 'Kathmandu',
                'addressCountry' => 'NP',
            ],
            'sameAs' => $sameAs,
        ];

        $lat = Setting::get('map_latitude');
        $lon = Setting::get('map_longitude');

        if (is_numeric($lat) && is_numeric($lon)) {
            $data['geo'] = [
                '@type' => 'GeoCoordinates',
                'latitude' => (float) $lat,
                'longitude' => (float) $lon,
            ];
        }

        return array_filter($data, fn ($value) => $value !== null && $value !== '' && $value !== []);
    }

    /**
     * A course, with the institute as provider and the fee as the offer.
     *
     * @return array<string, mixed>
     */
    public static function course(Service $service): array
    {
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Course',
            'name' => (string) $service->title,
            'description' => (string) ($service->summary ?: $service->meta_description ?: $service->title),
            'url' => route('web.courses.show', $service->slug),
            'provider' => [
                '@type' => 'EducationalOrganization',
                '@id' => url('/').'#organization',
                'name' => (string) Setting::get('site_name', config('app.name')),
            ],
            'inLanguage' => ['de', 'en'],
        ];

        if ($service->duration) {
            $data['timeRequired'] = (string) $service->duration;
        }

        if ($service->fee !== null) {
            $data['offers'] = [
                '@type' => 'Offer',
                'price' => (string) (int) round((float) $service->fee),
                'priceCurrency' => 'NPR',
                'availability' => 'https://schema.org/InStock',
                'url' => route('web.courses.show', $service->slug),
            ];
        }

        return $data;
    }

    /**
     * A blog article.
     *
     * @return array<string, mixed>
     */
    public static function blogPosting(Post $post): array
    {
        $published = $post->published_at ?? $post->created_at;

        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => (string) $post->title,
            'description' => (string) ($post->summary ?: $post->meta_description ?: $post->title),
            'url' => route('web.blog.show', $post->slug),
            'mainEntityOfPage' => route('web.blog.show', $post->slug),
            'datePublished' => $published?->toAtomString(),
            'dateModified' => $post->updated_at?->toAtomString(),
            'author' => [
                '@type' => 'Organization',
                'name' => (string) Setting::get('site_name', config('app.name')),
            ],
            'publisher' => ['@id' => url('/').'#organization'],
        ];

        $image = $post->getFirstMediaUrl('featured_image');

        if ($image) {
            $data['image'] = $image;
        }

        return array_filter($data, fn ($value) => $value !== null && $value !== '');
    }

    /**
     * Breadcrumb trail. Each crumb is [label, url]; the last one may have no URL.
     *
     * @param  array<int, array{0: string, 1: ?string}>  $crumbs
     * @return array<string, mixed>
     */
    public static function breadcrumbs(array $crumbs): array
    {
        $items = [];

        foreach (array_values($crumbs) as $index => [$label, $url]) {
            $item = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $label,
            ];

            if ($url !== null) {
                $item['item'] = $url;
            }

            $items[] = $item;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    private static function absolute(mixed $path): ?string
    {
        if (! is_string($path) || $path === '') {
            return null;
        }

        return str_starts_with($path, 'http') ? $path : url($path);
    }
}
