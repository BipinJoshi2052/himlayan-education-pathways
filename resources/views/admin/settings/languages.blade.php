<div class="card">
    <div class="card-header"><h5 class="mb-0">Languages</h5></div>
    <div class="card-body">
        <p class="text-muted small">Choose which languages appear in the website's language switcher, and which locale tabs show up when editing content in the admin panel. English can't be turned off — it's the fallback locale.</p>

        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            <input type="hidden" name="group" value="languages">

            @php
                $localesWeb = \App\Models\Setting::get('locales_web') ?? array_keys($allLocales);
                $localesAdmin = \App\Models\Setting::get('locales_admin') ?? array_keys($allLocales);
            @endphp

            <div class="row">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Website</label>
                    <p class="text-muted small">Languages a visitor can pick from the switcher.</p>
                    <input type="hidden" name="locales_web[]" value="en">
                    @foreach ($allLocales as $code => $label)
                        <div class="form-check">
                            @if ($code === 'en')
                                <input type="checkbox" class="form-check-input" checked disabled>
                                <label class="form-check-label">
                                    @if ($flag = \App\Common\Services\LocaleOptions::flagCode($code))
                                        <span class="fi fi-{{ $flag }} me-1"></span>
                                    @endif
                                    {{ $label }} <small class="text-muted">(always on)</small>
                                </label>
                            @else
                                <input type="checkbox" class="form-check-input" id="locales_web_{{ $code }}"
                                       name="locales_web[]" value="{{ $code }}" {{ in_array($code, $localesWeb, true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="locales_web_{{ $code }}">
                                    @if ($flag = \App\Common\Services\LocaleOptions::flagCode($code))
                                        <span class="fi fi-{{ $flag }} me-1"></span>
                                    @endif
                                    {{ $label }}
                                </label>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Admin Panel</label>
                    <p class="text-muted small">Locale tabs shown when editing posts, services, galleries, and settings.</p>
                    <input type="hidden" name="locales_admin[]" value="en">
                    @foreach ($allLocales as $code => $label)
                        <div class="form-check">
                            @if ($code === 'en')
                                <input type="checkbox" class="form-check-input" checked disabled>
                                <label class="form-check-label">
                                    @if ($flag = \App\Common\Services\LocaleOptions::flagCode($code))
                                        <span class="fi fi-{{ $flag }} me-1"></span>
                                    @endif
                                    {{ $label }} <small class="text-muted">(always on)</small>
                                </label>
                            @else
                                <input type="checkbox" class="form-check-input" id="locales_admin_{{ $code }}"
                                       name="locales_admin[]" value="{{ $code }}" {{ in_array($code, $localesAdmin, true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="locales_admin_{{ $code }}">
                                    @if ($flag = \App\Common\Services\LocaleOptions::flagCode($code))
                                        <span class="fi fi-{{ $flag }} me-1"></span>
                                    @endif
                                    {{ $label }}
                                </label>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Save Language Settings</button>
        </form>
    </div>
</div>
