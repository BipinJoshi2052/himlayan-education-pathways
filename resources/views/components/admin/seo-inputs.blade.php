{{--
    Usage: @include('components.admin.seo-inputs', ['model' => $post, 'locales' => $locales])
    ($model may be null for a create form.)

    Note: "canonical URL" was asked for alongside meta_title/meta_description,
    but none of the posts/services/galleries/sections migrations actually
    have a canonical_url column (their own schema specs only listed
    meta_title/meta_description/meta_keywords). The field is rendered here
    since it was explicitly requested, but nothing currently persists it —
    it's silently dropped by mass-assignment on every module. Add a
    canonical_url column (and fillable entry) to a model's migration before
    relying on it actually saving anything.
--}}
@php
    $model ??= null;
    $locales ??= ['en'];

    // Spatie throws AttributeIsNotTranslatable if you call getTranslation()
    // for a field the model didn't declare translatable — Gallery has no
    // meta_keywords column, unlike Post/Service, so this can't call
    // getTranslation() unconditionally the way the rest of this component does.
    $seoValue = function (string $field, string $locale) use ($model) {
        if ($model === null || ! in_array($field, $model->translatable ?? [], true)) {
            return null;
        }

        return $model->getTranslation($field, $locale, false);
    };
    $hasMetaKeywords = $model !== null && in_array('meta_keywords', $model->translatable ?? [], true);
@endphp

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">SEO</h5>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label">Meta Title</label>
            @foreach ($locales as $i => $locale)
                <div data-locale-pane="{{ $locale }}" style="{{ $i === 0 ? '' : 'display:none' }}">
                    <input type="text" class="form-control mb-2" name="meta_title[{{ $locale }}]"
                       placeholder="{{ count($locales) > 1 ? strtoupper($locale).' — Meta Title' : 'Meta Title' }}"
                       value="{{ old('meta_title.'.$locale, $seoValue('meta_title', $locale)) }}">
                </div>
            @endforeach
        </div>

        <div class="form-group">
            <label class="form-label">Meta Description</label>
            @foreach ($locales as $i => $locale)
                <div data-locale-pane="{{ $locale }}" style="{{ $i === 0 ? '' : 'display:none' }}">
                    <textarea class="form-control mb-2" rows="2" name="meta_description[{{ $locale }}]"
                          placeholder="{{ count($locales) > 1 ? strtoupper($locale).' — Meta Description' : 'Meta Description' }}">{{ old('meta_description.'.$locale, $seoValue('meta_description', $locale)) }}</textarea>
                </div>
            @endforeach
        </div>

        @if ($hasMetaKeywords)
            <div class="form-group mb-0">
                <label class="form-label">Meta Keywords</label>
                @foreach ($locales as $i => $locale)
                    <div data-locale-pane="{{ $locale }}" style="{{ $i === 0 ? '' : 'display:none' }}">
                        <input type="text" class="form-control mb-2" name="meta_keywords[{{ $locale }}]"
                           placeholder="{{ count($locales) > 1 ? strtoupper($locale).' — comma separated' : 'comma separated' }}"
                           value="{{ old('meta_keywords.'.$locale, $seoValue('meta_keywords', $locale)) }}">
                    </div>
                @endforeach
            </div>
        @endif

        <div class="form-group mb-0 mt-3">
            <label class="form-label">Canonical URL</label>
            <input type="text" class="form-control" name="canonical_url" value="{{ old('canonical_url') }}">
            <small class="text-muted">Not currently persisted — see note at the top of this file.</small>
        </div>
    </div>
</div>
