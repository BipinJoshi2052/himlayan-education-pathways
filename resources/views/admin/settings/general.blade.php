<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="group" value="general">

            <div class="form-group">
                <label class="form-label">Logo</label>
                @if (\App\Models\Setting::get('site_logo'))
                    <div class="mb-2">
                        <img src="{{ \App\Models\Setting::get('site_logo') }}" alt="Current logo" style="max-height: 60px;">
                    </div>
                @endif
                <input type="file" class="form-control" name="site_logo" accept="image/*">
                <small class="text-muted">Shown in the site navbar/footer and used as the browser favicon. A square or wide transparent PNG/SVG works best.</small>
            </div>
            <div class="form-group">
                <label class="form-label">Site Name</label>
                <input type="text" class="form-control" name="site_name" value="{{ old('site_name', \App\Models\Setting::get('site_name', config('app.name'))) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Tagline</label>
                <input type="text" class="form-control" name="site_tagline" value="{{ old('site_tagline', \App\Models\Setting::get('site_tagline')) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Address</label>
                <input type="text" class="form-control" name="site_address" value="{{ old('site_address', \App\Models\Setting::get('site_address')) }}">
            </div>

            <div class="row align-items-end">
                <div class="col-md-5">
                    <div class="form-group">
                        <label class="form-label">Map Latitude</label>
                        <input type="text" class="form-control" id="map_latitude" name="map_latitude" value="{{ old('map_latitude', \App\Models\Setting::get('map_latitude')) }}" placeholder="27.717850">
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label class="form-label">Map Longitude</label>
                        <input type="text" class="form-control" id="map_longitude" name="map_longitude" value="{{ old('map_longitude', \App\Models\Setting::get('map_longitude')) }}" placeholder="85.347109">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <button type="button" class="btn btn-soft-primary w-100" data-bs-toggle="modal" data-bs-target="#map-picker-modal">
                            <i class="ti-location-pin"></i> Pin
                        </button>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="map-picker-modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Pin the location</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted small mb-2">Click the map to drop the pin, then press "Use this location".</p>
                            <div id="map-picker" style="height: 420px; border-radius: 6px;"></div>
                            <div class="mt-2 small">Selected: <span id="map-picker-coords" class="text-muted">none yet</span></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="map-picker-use" disabled data-bs-dismiss="modal">Use this location</button>
                        </div>
                    </div>
                </div>
            </div>

            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">
            <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
            <script>
                (function () {
                    var modal = document.getElementById('map-picker-modal');
                    var latInput = document.getElementById('map_latitude');
                    var lngInput = document.getElementById('map_longitude');
                    var coordsLabel = document.getElementById('map-picker-coords');
                    var useBtn = document.getElementById('map-picker-use');
                    var map = null;
                    var marker = null;
                    var picked = null;

                    modal.addEventListener('shown.bs.modal', function () {
                        var savedLat = parseFloat(latInput.value);
                        var savedLng = parseFloat(lngInput.value);
                        var hasSaved = !isNaN(savedLat) && !isNaN(savedLng);
                        var center = hasSaved ? [savedLat, savedLng] : [27.717850, 85.347109];

                        if (!map) {
                            map = L.map('map-picker').setView(center, hasSaved ? 17 : 15);
                            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                maxZoom: 19,
                                attribution: '&copy; OpenStreetMap contributors',
                            }).addTo(map);

                            map.on('click', function (e) {
                                picked = e.latlng;
                                if (marker) {
                                    marker.setLatLng(picked);
                                } else {
                                    marker = L.marker(picked).addTo(map);
                                }
                                coordsLabel.textContent = picked.lat.toFixed(6) + ', ' + picked.lng.toFixed(6);
                                useBtn.disabled = false;
                            });
                        } else {
                            map.setView(center, hasSaved ? 17 : 15);
                        }

                        if (hasSaved && !marker) {
                            marker = L.marker(center).addTo(map);
                        }

                        setTimeout(function () { map.invalidateSize(); }, 150);
                    });

                    useBtn.addEventListener('click', function () {
                        if (!picked) return;
                        latInput.value = picked.lat.toFixed(6);
                        lngInput.value = picked.lng.toFixed(6);
                    });
                })();
            </script>
            <div class="form-group">
                <label class="form-label">Contact Email</label>
                <input type="email" class="form-control" name="contact_email" value="{{ old('contact_email', \App\Models\Setting::get('contact_email')) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Contact Phone</label>
                <input type="text" class="form-control" name="contact_phone" value="{{ old('contact_phone', \App\Models\Setting::get('contact_phone')) }}">
                <small class="text-muted">Add more than one number separated by commas, e.g. 9801234567, 01-4123456. Each one is shown and can be tapped to call.</small>
            </div>
            <div class="form-group mb-0">
                <label class="form-label">Admin Notification Email</label>
                <input type="email" class="form-control" name="admin_notification_email"
                       value="{{ old('admin_notification_email', \App\Models\Setting::get('admin_notification_email')) }}">
                <small class="text-muted">Where new contact-form inquiries are emailed. Falls back to the site's logged-in admin account email if unset.</small>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Save General Settings</button>
        </form>
    </div>
</div>
