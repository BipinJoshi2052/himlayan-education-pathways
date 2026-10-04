<div class="card">
    <div class="card-header"><h5 class="mb-0">Appearance</h5></div>
    <div class="card-body">
        <p class="text-muted small">This is the public site's default brand color — separate from each admin user's own <code>theme_mode</code>/<code>primary_color</code> (set on their account, see docs/admin-ui.md).</p>

        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            <input type="hidden" name="group" value="appearance">

            <div class="form-group">
                <label class="form-label">Primary Color</label>
                <input type="color" class="form-control" name="primary_color" style="max-width: 120px;"
                       value="{{ old('primary_color', \App\Models\Setting::get('primary_color', '#3a57e8')) }}">
                <small class="text-muted">Admin panel default (when a user hasn't set their own) and the public site's main accent color (buttons, links, highlights).</small>
            </div>

            <div class="form-group mb-0">
                <label class="form-label">Secondary Color</label>
                <input type="color" class="form-control" name="secondary_color" style="max-width: 120px;"
                       value="{{ old('secondary_color', \App\Models\Setting::get('secondary_color', '#f26b65')) }}">
                <small class="text-muted">Used for hover states and secondary highlights on the public site.</small>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Save Appearance Settings</button>
        </form>
    </div>
</div>
