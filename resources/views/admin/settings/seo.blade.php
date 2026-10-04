@php
    $metaTitle = \App\Models\Setting::get('seo_meta_title', []);
    $metaDescription = \App\Models\Setting::get('seo_meta_description', []);
    $metaKeywords = \App\Models\Setting::get('seo_meta_keywords', []);
@endphp

<div class="card mb-4">
    <div class="card-header"><h5 class="mb-0">Discovery Files</h5></div>
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('sitemap') }}" target="_blank" rel="noopener" class="btn btn-soft-secondary btn-sm">
                sitemap.xml <i class="ti-arrow-top-right"></i>
            </a>
            <a href="{{ route('robots') }}" target="_blank" rel="noopener" class="btn btn-soft-secondary btn-sm">
                robots.txt <i class="ti-arrow-top-right"></i>
            </a>
            <a href="{{ route('llms.summary') }}" target="_blank" rel="noopener" class="btn btn-soft-secondary btn-sm">
                llms.txt <i class="ti-arrow-top-right"></i>
            </a>
            <a href="{{ route('llms.full') }}" target="_blank" rel="noopener" class="btn btn-soft-secondary btn-sm">
                llms-full.txt <i class="ti-arrow-top-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header"><h5 class="mb-0">Global Default SEO</h5></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="group" value="seo">

            @include('admin.partials.locale-tabs', ['locales' => $locales])

            <div class="form-group">
                <label class="form-label">Default Meta Title</label>
                @foreach ($locales as $i => $locale)
                    <div data-locale-pane="{{ $locale }}" style="{{ $i === 0 ? '' : 'display:none' }}">
                        <input type="text" class="form-control seo-title-input" maxlength="60"
                               name="seo_meta_title[{{ $locale }}]"
                               value="{{ old('seo_meta_title.'.$locale, is_array($metaTitle) ? ($metaTitle[$locale] ?? '') : '') }}">
                        <small class="text-muted"><span class="seo-title-count">0</span>/60 characters</small>
                    </div>
                @endforeach
            </div>

            <div class="form-group">
                <label class="form-label">Default Meta Description</label>
                @foreach ($locales as $i => $locale)
                    <div data-locale-pane="{{ $locale }}" style="{{ $i === 0 ? '' : 'display:none' }}">
                        <textarea class="form-control seo-description-input" rows="3" maxlength="160"
                                  name="seo_meta_description[{{ $locale }}]">{{ old('seo_meta_description.'.$locale, is_array($metaDescription) ? ($metaDescription[$locale] ?? '') : '') }}</textarea>
                        <small class="text-muted"><span class="seo-description-count">0</span>/160 characters</small>
                    </div>
                @endforeach
            </div>

            <div class="form-group mb-0">
                <label class="form-label">Default Meta Keywords</label>
                @foreach ($locales as $i => $locale)
                    <div data-locale-pane="{{ $locale }}" style="{{ $i === 0 ? '' : 'display:none' }}">
                        <input type="text" class="form-control" placeholder="comma, separated, keywords"
                               name="seo_meta_keywords[{{ $locale }}]"
                               value="{{ old('seo_meta_keywords.'.$locale, is_array($metaKeywords) ? ($metaKeywords[$locale] ?? '') : '') }}">
                    </div>
                @endforeach
            </div>

            <hr class="hr-horizontal my-4">

            <div class="form-group">
                <label class="form-label">Default OpenGraph Image (fallback when a page has none of its own)</label>
                @if (\App\Models\Setting::get('seo_default_og_image'))
                    <img src="{{ \App\Models\Setting::get('seo_default_og_image') }}" class="img-fluid rounded mb-2" style="max-width: 300px;" alt="">
                @endif
                <input type="file" class="form-control" name="seo_default_og_image" accept="image/*">
            </div>

            <div class="form-group">
                <label class="form-label">Google Search Console Verification Code</label>
                <input type="text" class="form-control" name="google_site_verification"
                       value="{{ old('google_site_verification', \App\Models\Setting::get('google_site_verification')) }}">
            </div>

            <div class="form-group mb-0">
                <label class="form-label">Google Analytics Measurement ID</label>
                <input type="text" class="form-control" name="google_analytics_id" placeholder="G-XXXXXXXXXX"
                       value="{{ old('google_analytics_id', \App\Models\Setting::get('google_analytics_id')) }}" style="max-width: 320px;">
                <small class="text-muted">A GA4 Measurement ID (starts with "G-"). When set, the tracking script loads on every public page.</small>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Save SEO Settings</button>
        </form>

        <hr class="hr-horizontal my-4">

        <form method="POST" action="{{ route('admin.settings.flush-seo-cache') }}">
            @csrf
            <button type="submit" class="btn btn-soft-secondary">Flush SEO Cache</button>
            <small class="text-muted ms-2">Forces the sitemap and llms.txt to regenerate on their next request.</small>
        </form>
    </div>
</div>

<script>
    (function () {
        function wireCounter(inputSelector, countSelector, max) {
            document.querySelectorAll(inputSelector).forEach(function (input) {
                var counter = input.parentElement.querySelector(countSelector);
                var update = function () {
                    counter.textContent = Math.min(input.value.length, max);
                };
                input.addEventListener('input', update);
                update();
            });
        }

        wireCounter('.seo-title-input', '.seo-title-count', 60);
        wireCounter('.seo-description-input', '.seo-description-count', 160);
    })();
</script>
