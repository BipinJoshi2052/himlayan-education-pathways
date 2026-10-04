<?php

declare(strict_types=1);

namespace App\Common\Services;

use App\Models\Setting;

/**
 * Settings > General map_latitude/map_longitude drive a precise pin on
 * OpenStreetMap (free, no API key) — see docs/public-website.md. Falls
 * back to a Google Maps address search when no coordinates are saved yet,
 * so the site keeps working before an admin fills them in.
 */
final class MapLink
{
    /**
     * Roughly ~500m box around the point, in degrees.
     */
    private const BBOX_DELTA = 0.005;

    public static function embedUrl(): ?string
    {
        $coords = self::coordinates();

        if ($coords === null) {
            return null;
        }

        [$lat, $lon] = $coords;

        $bbox = sprintf(
            '%F,%F,%F,%F',
            $lon - self::BBOX_DELTA,
            $lat - self::BBOX_DELTA,
            $lon + self::BBOX_DELTA,
            $lat + self::BBOX_DELTA,
        );

        return "https://www.openstreetmap.org/export/embed.html?bbox={$bbox}&layer=mapnik&marker={$lat},{$lon}";
    }

    public static function pointUrl(): ?string
    {
        $coords = self::coordinates();

        if ($coords === null) {
            return null;
        }

        [$lat, $lon] = $coords;

        return "https://www.openstreetmap.org/?mlat={$lat}&mlon={$lon}#map=17/{$lat}/{$lon}";
    }

    /**
     * Turn-by-turn directions specifically open in Google Maps (not
     * OpenStreetMap) — Google's directions flow is what most visitors
     * actually have installed/expect on mobile, so this is a deliberate
     * exception to the OSM-first rule above.
     */
    public static function directionsUrl(string $fallbackAddress): string
    {
        $coords = self::coordinates();

        $destination = $coords !== null
            ? $coords[0].','.$coords[1]
            : $fallbackAddress;

        return 'https://www.google.com/maps/dir/?api=1&destination='.urlencode($destination);
    }

    /**
     * @return array{0: float, 1: float}|null
     */
    private static function coordinates(): ?array
    {
        $lat = Setting::get('map_latitude');
        $lon = Setting::get('map_longitude');

        if (! is_numeric($lat) || ! is_numeric($lon)) {
            return null;
        }

        return [(float) $lat, (float) $lon];
    }
}
