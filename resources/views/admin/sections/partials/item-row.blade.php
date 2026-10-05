@php
    $item ??= [];
    $get = fn (string $key, $default = null) => $item[$key] ?? $default;
    $itemTitles = $get('title', []);
    $itemDescriptions = $get('description', []);
@endphp
<div class="card item-row mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <h6 class="mb-3">Item</h6>
            <button type="button" class="btn-close remove-item-btn" aria-label="Remove item"></button>
        </div>

        @if ($get('id'))
            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $get('id') }}">
        @endif

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Title</label>
                    @foreach ($locales as $i => $locale)
                        <div data-locale-pane="{{ $locale }}" style="{{ $i === 0 ? '' : 'display:none' }}">
                            <input type="text" class="form-control mb-2" name="items[{{ $index }}][title][{{ $locale }}]"
                                   value="{{ is_array($itemTitles) ? ($itemTitles[$locale] ?? '') : '' }}"
                                   @if ($locale === 'en') required @endif>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Icon / badge</label>
                    <input type="text" class="form-control" name="items[{{ $index }}][icon_or_badge]" value="{{ $get('icon_or_badge') }}">
                </div>
            </div>
        </div>

        @include('components.admin.rich-text', [
            'name' => "items[{$index}][description]",
            'values' => is_array($itemDescriptions) ? $itemDescriptions : [],
            'locales' => $locales,
            'label' => 'Description',
        ])

        <div class="row">
            <div class="col-md-8">
                <div class="form-group mb-0">
                    <label class="form-label">Link URL</label>
                    <input type="text" class="form-control" name="items[{{ $index }}][link_url]" value="{{ $get('link_url') }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-0">
                    <label class="form-label">Sort order</label>
                    <input type="number" class="form-control" name="items[{{ $index }}][sort_order]" value="{{ $get('sort_order', 0) }}">
                </div>
            </div>
        </div>

        <div class="form-group mb-0 mt-3">
            <label class="form-label">Image</label>
            @if ($get('image_url'))
                @include('admin.partials.existing-image', [
                    'url' => $get('image_url'),
                    'alt' => 'Current image',
                    'removeName' => 'items['.$index.'][remove_image]',
                    'style' => 'max-height: 90px;',
                ])
            @endif
            <input type="file" class="form-control" name="items[{{ $index }}][image]" accept="image/*">
            <small class="text-muted">Used by layouts that show a per-item photo (team members, testimonials, hero slides, ...). Leave blank to keep the current image.</small>
        </div>
    </div>
</div>
