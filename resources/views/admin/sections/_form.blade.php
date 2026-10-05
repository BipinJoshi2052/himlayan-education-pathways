@php
    // old('items') takes priority so a failed validation round-trip redisplays
    // exactly what the admin typed/added in the repeater, not just what's
    // already saved in the DB — otherwise a validation error would silently
    // discard any items the user had entered. title/description are full
    // locale => value arrays (via getTranslations), not the current-locale
    // string, so the per-locale tabs below have something to render.
    $itemsToShow = old('items') ?? ($section->exists
        ? $section->items->map(fn ($item) => [
            'id' => $item->id,
            'title' => $item->getTranslations('title'),
            'description' => $item->getTranslations('description'),
            'icon_or_badge' => $item->icon_or_badge,
            'link_url' => $item->link_url,
            'sort_order' => $item->sort_order,
            'image_url' => $item->getFirstMediaUrl('image') ?: null,
        ])->all()
        : []);
@endphp

@include('admin.partials.locale-tabs', ['locales' => $locales])

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Content</h5></div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Title</label>
                    @foreach ($locales as $i => $locale)
                        <div data-locale-pane="{{ $locale }}" style="{{ $i === 0 ? '' : 'display:none' }}">
                            <input type="text" class="form-control mb-2" name="title[{{ $locale }}]"
                                   value="{{ old('title.'.$locale, $section->getTranslation('title', $locale, false)) }}"
                                   @if ($locale === 'en') required @endif>
                        </div>
                    @endforeach
                </div>

                <div class="form-group">
                    <label class="form-label">Subtitle</label>
                    @foreach ($locales as $i => $locale)
                        <div data-locale-pane="{{ $locale }}" style="{{ $i === 0 ? '' : 'display:none' }}">
                            <input type="text" class="form-control mb-2" name="subtitle[{{ $locale }}]"
                                   value="{{ old('subtitle.'.$locale, $section->getTranslation('subtitle', $locale, false)) }}">
                        </div>
                    @endforeach
                </div>

                @include('components.admin.rich-text', [
                    'name' => 'content', 'model' => $section, 'locales' => $locales, 'label' => 'Content',
                ])
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Image</h5></div>
            <div class="card-body">
                @if ($section->exists && $section->getFirstMediaUrl('image'))
                    @include('admin.partials.existing-image', [
                        'url' => $section->getFirstMediaUrl('image'),
                        'alt' => 'Current image',
                        'removeName' => 'remove_image',
                        'style' => 'max-height: 120px;',
                    ])
                @endif
                <input type="file" class="form-control" name="image" accept="image/*">
                <small class="text-muted">Used by layouts that show a single image for this section. Leave blank to keep the current image, or to use the page's built-in default if none has been set yet.</small>
            </div>
        </div>

        <div id="section-settings-box" class="card mb-4" style="display:none">
            <div class="card-header"><h5 class="mb-0">Section options</h5></div>
            <div class="card-body">
                @include('admin.sections.partials.settings-fields')
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Items</h5>
                <button type="button" class="btn btn-sm btn-soft-primary" id="add-item-btn">+ Add item</button>
            </div>
            <div class="card-body">
                <p class="text-muted small">Used by the Repeater and Cards layouts. Ignored for Single Block.</p>

                <div id="items-container">
                    @foreach ($itemsToShow as $index => $item)
                        @include('admin.sections.partials.item-row', ['index' => $index, 'item' => $item, 'locales' => $locales])
                    @endforeach
                </div>

                <template id="item-row-template">
                    @include('admin.sections.partials.item-row', ['index' => '__INDEX__', 'item' => null, 'locales' => $locales])
                </template>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Details</h5></div>
            <div class="card-body">
                <div class="form-group">
                    <label for="page_slug" class="form-label">Page</label>
                    <select class="form-select" id="page_slug" name="page_slug" required>
                        <option value="">Choose a page…</option>
                        @foreach ($catalog as $slug => $page)
                            <option value="{{ $slug }}" @selected(old('page_slug', $section->page_slug) === $slug)>{{ $page['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="key" class="form-label">Section component</label>
                    <select class="form-select" id="key" name="key" required>
                        <option value="">Choose a page first…</option>
                    </select>
                    <small class="text-muted">Components already added to this page are greyed out.</small>
                </div>

                <div class="form-group">
                    <small class="text-muted">Layout: <strong id="layout_label">—</strong> (set automatically by the component)</small>
                    <input type="hidden" name="layout_type" id="layout_type" value="{{ old('layout_type', $section->layout_type?->value) }}">
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                           @checked(old('is_active', $section->exists ? $section->is_active : true))>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>

                <div class="form-group mb-0">
                    <label for="sort_order" class="form-label">Sort order</label>
                    <input type="number" class="form-control" id="sort_order" name="sort_order" value="{{ old('sort_order', $section->sort_order ?? 0) }}">
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100">{{ $section->exists ? 'Update Section' : 'Create Section' }}</button>
    </div>
</div>

<script>
    (function () {
        const catalog = @json($catalog);
        const taken = @json($takenKeys);
        const layoutLabels = {single_block: 'Single Block', repeater: 'Repeater', cards: 'Cards'};
        const pageSel = document.getElementById('page_slug');
        const keySel = document.getElementById('key');
        const layoutInput = document.getElementById('layout_type');
        const layoutLabel = document.getElementById('layout_label');
        const initialKey = @json(old('key', $section->key));

        function render(selectedKey) {
            const page = pageSel.value;
            const components = (catalog[page] || {}).components || {};
            const takenHere = taken[page] || [];

            keySel.innerHTML = '';
            if (!page) {
                keySel.add(new Option('Choose a page first…', ''));
            } else {
                keySel.add(new Option('Choose a component…', ''));
                Object.entries(components).forEach(function ([key, c]) {
                    const isTaken = takenHere.includes(key) && key !== selectedKey;
                    const opt = new Option(c.label + (isTaken ? ' (already added)' : ''), key);
                    opt.disabled = isTaken;
                    keySel.add(opt);
                });
            }

            if (selectedKey && components[selectedKey]) {
                keySel.value = selectedKey;
            }
            updateLayout();
        }

        function updateLayout() {
            const component = ((catalog[pageSel.value] || {}).components || {})[keySel.value];
            const layout = component ? component.layout : '';
            layoutInput.value = layout;
            layoutLabel.textContent = layout ? (layoutLabels[layout] || layout) : '—';
            showSettings();
        }

        // Only the chosen component's options are enabled, so the hidden
        // groups (other components' fields) never submit.
        function showSettings() {
            const box = document.getElementById('section-settings-box');
            let anyShown = false;
            document.querySelectorAll('.section-settings-group').forEach(function (group) {
                const match = group.dataset.page === pageSel.value && group.dataset.key === keySel.value;
                group.style.display = match ? '' : 'none';
                group.querySelectorAll('input, select').forEach(function (el) { el.disabled = !match; });
                if (match) { anyShown = true; }
            });
            box.style.display = anyShown ? '' : 'none';
            syncCustomLinks();
        }

        function syncCustomLinks() {
            document.querySelectorAll('.section-link-select').forEach(function (sel) {
                const custom = sel.parentElement.querySelector('.section-link-custom');
                custom.style.display = sel.value === 'custom' ? '' : 'none';
            });
        }
        document.addEventListener('change', function (e) {
            if (e.target.classList && e.target.classList.contains('section-link-select')) { syncCustomLinks(); }
        });

        pageSel.addEventListener('change', function () { render(null); });
        keySel.addEventListener('change', updateLayout);
        document.addEventListener('DOMContentLoaded', function () { render(initialKey); });
    })();
</script>

<script>
    (function () {
        const container = document.getElementById('items-container');
        const template = document.getElementById('item-row-template').innerHTML;
        let nextIndex = {{ count($itemsToShow) }};

        document.getElementById('add-item-btn').addEventListener('click', function () {
            const html = template.replaceAll('__INDEX__', nextIndex);
            const wrapper = document.createElement('div');
            wrapper.innerHTML = html.trim();
            const newRow = wrapper.firstElementChild;
            container.appendChild(newRow);

            // Quill measures its container's layout at init time — an
            // editor created inside a display:none pane ends up with
            // broken (often zero) dimensions. The server renders only the
            // first locale's pane visible, so force every pane visible
            // first, initialize, then apply the real tab visibility.
            const allPanes = newRow.querySelectorAll('[data-locale-pane]');
            allPanes.forEach(function (pane) { pane.style.display = ''; });

            if (window.initRichTextField) {
                newRow.querySelectorAll('.rich-text-field').forEach(function (field) {
                    window.initRichTextField(field);
                });
            }

            // New rows are rendered server-side with the first locale's pane
            // visible — match whichever tab is actually active right now,
            // otherwise a freshly-added row ignores the admin's current tab.
            // With only one locale, no switcher exists at all (see
            // locale-tabs.blade.php) — fall back to that row's own first
            // pane so the single-locale case still shows something.
            const activeBtn = document.querySelector('.locale-switch-btn.active');
            const activeLocale = activeBtn
                ? activeBtn.dataset.locale
                : (allPanes[0] ? allPanes[0].dataset.localePane : null);
            allPanes.forEach(function (pane) {
                pane.style.display = pane.dataset.localePane === activeLocale ? '' : 'none';
            });

            nextIndex++;
        });

        container.addEventListener('click', function (event) {
            const removeBtn = event.target.closest('.remove-item-btn');
            if (removeBtn) {
                removeBtn.closest('.item-row').remove();
            }
        });
    })();
</script>
