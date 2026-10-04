<div class="card">
    <div class="card-header"><h5 class="mb-0">Widgets</h5></div>
    <div class="card-body">
        <p class="text-muted small">Site-wide floating widgets shown on every public page.</p>

        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            <input type="hidden" name="group" value="widgets">

            <h6 class="mb-3">WhatsApp</h6>

            <div class="form-group">
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="whatsapp_enabled" name="whatsapp_enabled" value="1"
                           {{ old('whatsapp_enabled', \App\Models\Setting::get('whatsapp_enabled', false)) ? 'checked' : '' }}>
                    <label class="form-check-label" for="whatsapp_enabled">Show a floating WhatsApp button</label>
                </div>
            </div>

            <div class="form-group mb-0">
                <label class="form-label">WhatsApp Number</label>
                <input type="text" class="form-control" name="whatsapp_phone" placeholder="e.g. 9779812345678 (country code, no + or spaces)"
                       value="{{ old('whatsapp_phone', \App\Models\Setting::get('whatsapp_phone')) }}" style="max-width: 320px;">
                <small class="text-muted">Full number with country code, digits only — e.g. 9779812345678 for a Nepal number. Required for the button to appear even if the toggle above is on.</small>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Save Widget Settings</button>
        </form>
    </div>
</div>
