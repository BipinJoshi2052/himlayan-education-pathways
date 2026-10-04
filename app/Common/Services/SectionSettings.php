<?php

declare(strict_types=1);

namespace App\Common\Services;

use App\Models\Section;

/**
 * Reads a section's friendly settings (see config/sections.php) and turns
 * them into what the views need: text with defaults, real URLs for button
 * destinations, and a background class.
 */
final class SectionSettings
{
    /**
     * Button destinations the admin can pick from. A value of 'custom'
     * means the separate *_url field holds the address.
     */
    public const LINKS = [
        'courses' => ['label' => 'Courses page', 'route' => 'web.courses.index'],
        'contact' => ['label' => 'Contact page', 'route' => 'web.contact'],
        'about' => ['label' => 'About page', 'route' => 'web.about'],
        'blog' => ['label' => 'Blog', 'route' => 'web.blog.index'],
        'faq' => ['label' => 'FAQ', 'route' => 'web.faq'],
        'custom' => ['label' => 'Another web address (type it below)'],
    ];

    public const BACKGROUNDS = [
        'image' => 'Keep the page image (default)',
        'soft' => 'Soft grey',
        'brand' => 'Light brand colour',
    ];

    public static function fields(?string $page, ?string $key): array
    {
        return (array) config("sections.pages.$page.components.$key.settings", []);
    }

    public static function text(?Section $section, string $name): string
    {
        $fields = self::fields($section?->page_slug, $section?->key);
        $stored = $section?->settings[$name] ?? null;

        if (filled($stored)) {
            return (string) $stored;
        }

        return (string) ($fields[$name]['default'] ?? '');
    }

    /**
     * URL for a link-type field (e.g. 'primary_link'); null when unknown.
     */
    public static function url(?Section $section, string $name): ?string
    {
        $choice = self::choice($section, $name);

        if ($choice === 'custom') {
            $custom = $section?->settings[$name.'_url'] ?? null;

            return filled($custom) ? (string) $custom : null;
        }

        $route = self::LINKS[$choice]['route'] ?? null;

        return $route ? route($route) : null;
    }

    public static function backgroundClass(?Section $section): string
    {
        return match (self::choice($section, 'background')) {
            'soft' => 'sec-bg-soft',
            'brand' => 'sec-bg-brand',
            default => '',
        };
    }

    private static function choice(?Section $section, string $name): string
    {
        $stored = $section?->settings[$name] ?? null;
        $default = self::fields($section?->page_slug, $section?->key)[$name]['default'] ?? null;

        return (string) (filled($stored) ? $stored : $default);
    }
}
