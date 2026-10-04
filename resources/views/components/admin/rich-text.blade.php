{{--
    Usage (model-backed, e.g. a Post): @include('components.admin.rich-text', [
        'name' => 'content', 'model' => $post, 'locales' => $locales, 'label' => 'Content',
    ])
    Usage (plain array, e.g. a repeater item that isn't its own Eloquent
    model in the form): @include('components.admin.rich-text', [
        'name' => "items[{$index}][description]", 'values' => $itemDescriptions, 'locales' => $locales,
    ])
    One Quill editor per locale, each syncing its HTML into a hidden input
    named name[locale] on every edit (public/admin-assets/js/rich-text.js).
    Visibility per locale is driven by the page's shared locale-tabs switcher
    (data-locale-pane — see admin.partials.locale-tabs), not an editor-local
    tab control, so one English/Nepali toggle switches title, summary, AND
    this editor together, as asked.
--}}
@php
    $model ??= null;
    $values ??= null;
    $locales ??= ['en'];
    $label ??= null;
    // $name can be bracket-style ("items[0][description]") for repeater
    // rows — old() needs dot-notation ("items.0.description") to look that
    // up correctly, so this derives it rather than assuming $name already
    // is one.
    $oldKey = rtrim(str_replace(['[', ']'], ['.', ''], $name), '.');
@endphp

<div class="rich-text-field" data-field-name="{{ $name }}">
    @if ($label)
        <label class="form-label">{{ $label }}</label>
    @endif

    @foreach ($locales as $i => $locale)
        <div data-locale-pane="{{ $locale }}" style="{{ $i === 0 ? '' : 'display:none' }}">
            <div class="rich-text-editor"></div>
        </div>
        <input type="hidden" name="{{ $name }}[{{ $locale }}]" class="rich-text-hidden-input" data-locale="{{ $locale }}"
               value="{{ old($oldKey.'.'.$locale, is_array($values) ? ($values[$locale] ?? null) : $model?->getTranslation($name, $locale, false)) }}">
    @endforeach
</div>
