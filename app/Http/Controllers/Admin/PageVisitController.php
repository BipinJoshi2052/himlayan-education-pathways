<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageVisit;
use App\Services\IpGeolocationService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class PageVisitController extends Controller
{
    private const GROUPS = [
        'page' => 'Page',
        'ip' => 'IP address',
        'location' => 'Location',
        'source' => 'Source',
    ];

    /**
     * Host fragments matched against the referer to label common sources.
     * Checked in order, first match wins.
     *
     * @var array<string, string>
     */
    private const KNOWN_SOURCES = [
        'google.' => 'Google',
        'bing.' => 'Bing',
        'duckduckgo.' => 'DuckDuckGo',
        'yahoo.' => 'Yahoo',
        'facebook.' => 'Facebook',
        'fb.me' => 'Facebook',
        'instagram.' => 'Instagram',
        'tiktok.' => 'TikTok',
        't.co' => 'X (Twitter)',
        'twitter.' => 'X (Twitter)',
        'x.com' => 'X (Twitter)',
        'linkedin.' => 'LinkedIn',
        'youtube.' => 'YouTube',
        'whatsapp.' => 'WhatsApp',
        'wa.me' => 'WhatsApp',
    ];

    public function index(Request $request, IpGeolocationService $geolocation): View
    {
        [$from, $to] = $this->dateRange($request);
        $mode = $request->query('view') === 'map' ? 'map' : 'list';
        $group = array_key_exists((string) $request->query('group'), self::GROUPS) ? (string) $request->query('group') : null;

        $base = fn () => PageVisit::query()
            ->whereBetween('visited_at', [$from->startOfDay(), $to->endOfDay()])
            ->when($request->query('path'), fn (Builder $q, $path) => $q->where('path', 'like', '%'.$path.'%'));

        $data = [
            'mode' => $mode,
            'group' => $group,
            'groupLabels' => self::GROUPS,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'totalCount' => PageVisit::count(),
            'todayCount' => PageVisit::whereDate('visited_at', today())->count(),
        ];

        if ($mode === 'map') {
            $data['points'] = $this->mapPoints($base());
            $data['unlocatedCount'] = $base()->whereNull('location')->count();
            $data['rangeCount'] = $base()->count();

            return view('admin.visits.index', $data + ['groups' => collect(), 'visits' => null]);
        }

        if ($group) {
            $rows = $this->groupedRows($base(), $group);

            if ($group === 'source') {
                $rows = $rows->map(function ($row) {
                    $row->group_key = $this->labelHost((string) $row->group_key);

                    return $row;
                });
            }

            return view('admin.visits.index', $data + [
                'groups' => $rows,
                'visits' => null,
            ]);
        }

        $visits = $base()->latest('visited_at')->paginate(10)->withQueryString();
        $this->resolveMissingLocations($base, $visits->getCollection(), $geolocation);

        return view('admin.visits.index', $data + ['groups' => collect(), 'visits' => $visits]);
    }

    /**
     * Start and end dates, defaulting to the last six months.
     *
     * @return array{0: CarbonImmutable, 1: CarbonImmutable}
     */
    private function dateRange(Request $request): array
    {
        $to = $this->parseDate($request->query('to')) ?? CarbonImmutable::today();
        $from = $this->parseDate($request->query('from')) ?? $to->subMonths(6);

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to, $from];
        }

        return [$from, $to];
    }

    private function parseDate(mixed $value): ?CarbonImmutable
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        try {
            return CarbonImmutable::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Visits counted per page, IP or location. The key is computed in a
     * subquery and grouped on its alias, which MySQL's strict grouping
     * accepts. Locations are read from saved values only; no API calls.
     */
    private function groupedRows(Builder $query, string $group)
    {
        $key = match ($group) {
            'page' => 'path',
            'ip' => 'ip_address',
            'location' => "COALESCE(NULLIF(CONCAT_WS(', ', JSON_UNQUOTE(JSON_EXTRACT(location, '\$.city')), JSON_UNQUOTE(JSON_EXTRACT(location, '\$.country'))), ''), 'Unknown')",
            // The host is extracted here; relabelled to a friendly name (or
            // 'Internal') by labelHost() once the rows come back.
            'source' => "CASE WHEN referer IS NULL OR referer = '' THEN 'Direct' ELSE SUBSTRING_INDEX(SUBSTRING_INDEX(referer, '://', -1), '/', 1) END",
        };

        $inner = $query->selectRaw("{$key} as group_key, visited_at");

        return DB::query()
            ->fromSub($inner, 't')
            ->selectRaw('group_key, COUNT(*) as visits, MAX(visited_at) as last_visit')
            ->groupBy('group_key')
            ->orderByDesc('visits')
            ->limit(200)
            ->get();
    }

    /**
     * Visits with a saved latitude/longitude, merged into points about 1 km
     * apart so the map stays readable.
     *
     * @return array<int, array{lat: float, lon: float, visits: int, label: string}>
     */
    private function mapPoints(Builder $query): array
    {
        $lat = "CAST(JSON_UNQUOTE(JSON_EXTRACT(location, '\$.latitude')) AS DECIMAL(9,4))";
        $lon = "CAST(JSON_UNQUOTE(JSON_EXTRACT(location, '\$.longitude')) AS DECIMAL(9,4))";
        $label = "COALESCE(NULLIF(CONCAT_WS(', ', JSON_UNQUOTE(JSON_EXTRACT(location, '\$.city')), JSON_UNQUOTE(JSON_EXTRACT(location, '\$.country'))), ''), 'Unknown')";

        $inner = $query
            ->whereNotNull('location')
            ->selectRaw("ROUND({$lat}, 2) as lat, ROUND({$lon}, 2) as lon, {$label} as label")
            ->whereRaw("JSON_EXTRACT(location, '\$.latitude') IS NOT NULL")
            ->whereRaw("JSON_EXTRACT(location, '\$.longitude') IS NOT NULL");

        return DB::query()
            ->fromSub($inner, 't')
            ->selectRaw('lat, lon, COUNT(*) as visits, MAX(label) as label')
            ->groupBy('lat', 'lon')
            ->get()
            ->map(fn ($row) => [
                'lat' => (float) $row->lat,
                'lon' => (float) $row->lon,
                'visits' => (int) $row->visits,
                'label' => (string) $row->label,
            ])
            ->values()
            ->all();
    }

    /**
     * One lookup per distinct IP, only for IPs whose visits have no location
     * saved — but across the whole filtered date range, not just the page
     * currently on screen. Before this, an IP that never happened to land on
     * the first page of results was never resolved; most-recently-seen IPs
     * go first, and the lookup count is capped so one page load stays quick.
     * The result is written to every visit from that IP, so later loads
     * never call the API for them again.
     */
    private function resolveMissingLocations(callable $base, $currentPageVisits, IpGeolocationService $geolocation, int $limit = 15): void
    {
        $missingIps = $base()
            ->whereNull('location')
            ->selectRaw('ip_address, MAX(visited_at) as last_seen')
            ->groupBy('ip_address')
            ->orderByDesc('last_seen')
            ->limit($limit)
            ->pluck('ip_address');

        foreach ($missingIps as $ip) {
            $location = $geolocation->lookup($ip);

            if ($location === null) {
                continue;
            }

            PageVisit::whereNull('location')->where('ip_address', $ip)->update(['location' => json_encode($location)]);

            $currentPageVisits->each(function (PageVisit $v) use ($ip, $location) {
                if ($v->ip_address === $ip && $v->location === null) {
                    $v->location = $location;
                }
            });
        }
    }

    /**
     * Where a visit came from, for the Source column on the ungrouped list.
     */
    public static function sourceLabel(?string $referer): string
    {
        if (blank($referer)) {
            return 'Direct';
        }

        $host = parse_url($referer, PHP_URL_HOST);

        return self::labelHost($host !== null && $host !== '' ? $host : $referer);
    }

    /**
     * Turns a bare host (or the literal 'Direct') into a friendly label:
     * the site's own host becomes 'Internal' (a visitor clicking around the
     * site), known platforms get their name, anything else is shown as-is.
     */
    private static function labelHost(string $host): string
    {
        if ($host === 'Direct') {
            return $host;
        }

        $host = strtolower($host);
        $siteHost = strtolower((string) parse_url((string) config('app.url'), PHP_URL_HOST));

        if ($siteHost !== '' && str_contains($host, $siteHost)) {
            return 'Internal';
        }

        foreach (self::KNOWN_SOURCES as $needle => $label) {
            if (str_contains($host, $needle)) {
                return $label;
            }
        }

        return $host;
    }
}
