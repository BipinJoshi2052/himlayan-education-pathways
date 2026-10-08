<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Nominatim (OpenStreetMap's geocoder) has no IP-to-location endpoint at
 * all — it only geocodes addresses and coordinates. ipapi.co is used here
 * instead for the actual IP lookup (free tier, HTTPS, no API key needed at
 * this volume) — see docs/admin-ui.md.
 */
final class IpGeolocationService
{
    /**
     * @return array<string, mixed>|null
     */
    public function lookup(string $ip): ?array
    {
        $isPublicIp = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);

        if (! $isPublicIp) {
            return null;
        }

        try {
            $response = Http::timeout(5)->get("https://ipapi.co/{$ip}/json/");
        } catch (Throwable $e) {
            Log::warning('IP geolocation lookup failed.', ['ip' => $ip, 'error' => $e->getMessage()]);

            return null;
        }

        if (! $response->successful()) {
            Log::warning('IP geolocation lookup returned a non-2xx response.', ['ip' => $ip, 'status' => $response->status()]);

            return null;
        }

        $data = $response->json();

        if (! is_array($data) || isset($data['error'])) {
            // ipapi.co's free tier returns {"error": true, "reason": "..."} once its
            // rate limit is hit, with a 200 status — logged so a run of unresolved
            // locations can be told apart from genuinely private/reserved IPs.
            Log::warning('IP geolocation lookup returned an error.', ['ip' => $ip, 'reason' => $data['reason'] ?? null]);

            return null;
        }

        return [
            'city' => $data['city'] ?? null,
            'region' => $data['region'] ?? null,
            'country' => $data['country_name'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'looked_up_at' => now()->toIso8601String(),
        ];
    }
}
