@extends('layouts.admin')

@section('title', 'Page Visits')

@section('content')
    @php
        $tabQuery = request()->except('view');
    @endphp

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
        <h4 class="mb-0">Page Visits</h4>
        <div class="btn-group" role="group" aria-label="View">
            <a href="{{ route('admin.visits.index', $tabQuery) }}" class="btn btn-sm {{ $mode === 'list' ? 'btn-primary' : 'btn-outline-primary' }}">List</a>
            <a href="{{ route('admin.visits.index', array_merge($tabQuery, ['view' => 'map'])) }}" class="btn btn-sm {{ $mode === 'map' ? 'btn-primary' : 'btn-outline-primary' }}">Map</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card"><div class="card-body">
                <p class="mb-1 text-muted">Today</p>
                <h3 class="mb-0">{{ $todayCount }}</h3>
            </div></div>
        </div>
        <div class="col-md-6">
            <div class="card"><div class="card-body">
                <p class="mb-1 text-muted">All time</p>
                <h3 class="mb-0">{{ $totalCount }}</h3>
            </div></div>
        </div>
    </div>

    <form method="GET" class="row g-2 mb-3 align-items-end">
        <input type="hidden" name="view" value="{{ $mode }}">
        <div class="col-6 col-md-auto">
            <label class="form-label small text-muted mb-1">From</label>
            <input type="date" name="from" class="form-control" value="{{ $from }}">
        </div>
        <div class="col-6 col-md-auto">
            <label class="form-label small text-muted mb-1">To</label>
            <input type="date" name="to" class="form-control" value="{{ $to }}">
        </div>
        <div class="col-12 col-md-auto">
            <label class="form-label small text-muted mb-1">Page path</label>
            <input type="text" name="path" class="form-control" placeholder="e.g. /courses" value="{{ request('path') }}">
        </div>
        @if ($mode === 'list')
            <div class="col-12 col-md-auto">
                <label class="form-label small text-muted mb-1">Grouping</label>
                <select name="group" class="form-select">
                    <option value="">No grouping (each visit)</option>
                    @foreach ($groupLabels as $value => $label)
                        <option value="{{ $value }}" @selected($group === $value)>Group by {{ strtolower($label) }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="col-12 col-md-auto">
            <button type="submit" class="btn btn-outline-primary">Apply</button>
        </div>
    </form>

    @if ($mode === 'map')
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between flex-wrap gap-2 mb-2 small text-muted">
                    <span>{{ $rangeCount }} visits between {{ $from }} and {{ $to }}</span>
                    <span>{{ $unlocatedCount }} without a saved location (shown on the List view after lookup)</span>
                </div>
                @if (count($points) === 0)
                    <p class="text-muted mb-0 py-4 text-center">No located visits in this range yet. Open the List view to look up locations.</p>
                @endif
                <div id="visits-map" style="height: 480px; width: 100%; border-radius: 8px;" class="{{ count($points) === 0 ? 'd-none' : '' }}"></div>
                <p class="small text-muted mt-2 mb-0">Circles are sized by visit count. Map data &copy; OpenStreetMap contributors.</p>
            </div>
        </div>

        @if (count($points) > 0)
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">
            <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var points = @json($points);
                    var map = L.map('visits-map');
                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    var bounds = [];
                    points.forEach(function (p) {
                        var radius = Math.min(6 + Math.sqrt(p.visits) * 4, 30);
                        L.circleMarker([p.lat, p.lon], {
                            radius: radius,
                            color: '#525fe1',
                            fillColor: '#525fe1',
                            fillOpacity: 0.45,
                            weight: 2
                        })
                            .bindPopup('<strong>' + p.label + '</strong><br>' + p.visits + (p.visits === 1 ? ' visit' : ' visits'))
                            .addTo(map);
                        bounds.push([p.lat, p.lon]);
                    });

                    map.fitBounds(bounds, { padding: [30, 30], maxZoom: 8 });
                });
            </script>
        @endif
    @elseif ($group)
        <div class="card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>{{ $groupLabels[$group] }}</th>
                            <th class="text-end">Visits</th>
                            <th>Last visit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($groups as $row)
                            <tr>
                                <td class="small text-break">
                                    @if ($group === 'page')
                                        <code>{{ $row->group_key }}</code>
                                    @else
                                        {{ $row->group_key }}
                                    @endif
                                </td>
                                <td class="text-end fw-semibold">{{ $row->visits }}</td>
                                <td class="small text-nowrap">{{ \Illuminate\Support\Carbon::parse($row->last_visit)->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No visits recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <p class="small text-muted mt-2">Locations appear here once they've been looked up on the ungrouped list.</p>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>When</th>
                            <th>Page</th>
                            <th>IP</th>
                            <th>Location</th>
                            <th>Device</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($visits as $visit)
                            <tr>
                                <td class="small text-nowrap">{{ $visit->visited_at->format('Y-m-d H:i') }}</td>
                                <td class="small"><code>{{ $visit->path }}</code></td>
                                <td class="small">{{ $visit->ip_address }}</td>
                                <td class="small">
                                    @if ($visit->location && ($visit->location['city'] ?? $visit->location['country'] ?? null))
                                        {{ implode(', ', array_filter([$visit->location['city'] ?? null, $visit->location['region'] ?? null, $visit->location['country'] ?? null])) }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="small text-muted text-break" style="max-width: 320px;">{{ \Illuminate\Support\Str::limit($visit->user_agent, 90) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No visits recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $visits->links() }}
        </div>
    @endif
@endsection
