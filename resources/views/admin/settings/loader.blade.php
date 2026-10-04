<div class="card">
    <div class="card-header"><h5 class="mb-0">Loader</h5></div>
    <div class="card-body">
        <p class="text-muted small">Controls the page-load animation shown on the public website.</p>

        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="group" value="loader">

            @php
                $loaderMode = old('loader_mode', \App\Models\Setting::get('loader_mode', 'default'));
            @endphp

            <div class="form-group">
                <label class="form-label">Loader</label>
                <div class="form-check">
                    <input type="radio" class="form-check-input" id="loader_mode_default" name="loader_mode" value="default" {{ $loaderMode === 'default' ? 'checked' : '' }}>
                    <label class="form-check-label" for="loader_mode_default">Built-in loader (default)</label>
                </div>
                <div class="form-check">
                    <input type="radio" class="form-check-input" id="loader_mode_custom" name="loader_mode" value="custom" {{ $loaderMode === 'custom' ? 'checked' : '' }}>
                    <label class="form-check-label" for="loader_mode_custom">Custom image</label>
                </div>
                <div class="form-check">
                    <input type="radio" class="form-check-input" id="loader_mode_none" name="loader_mode" value="none" {{ $loaderMode === 'none' ? 'checked' : '' }}>
                    <label class="form-check-label" for="loader_mode_none">No loader — go straight to the page</label>
                </div>
            </div>

            <div class="form-group mb-0">
                <label class="form-label">Loader Image</label>
                @if (\App\Models\Setting::get('loader_image'))
                    <div class="mb-2">
                        <img src="{{ \App\Models\Setting::get('loader_image') }}" alt="Current loader image" style="max-height: 80px;">
                    </div>
                @endif
                <input type="file" class="form-control" name="loader_image" accept="image/*">
                <small class="text-muted">Only used when "Custom image" is selected above.</small>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Save Loader Settings</button>
        </form>
    </div>
</div>
