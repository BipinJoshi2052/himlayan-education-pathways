{{--
    Friendly settings fields for every component in config/sections.php.
    Only the group for the chosen page/component is enabled (JS in _form),
    so the hidden groups never submit. Values from the saved section are
    shown only for that section's own component; others show defaults.
--}}
@foreach ($catalog as $pageSlug => $page)
    @foreach ($page['components'] as $componentKey => $component)
        @php
            $fields = $component['settings'] ?? [];
            $isCurrent = $section->exists && $section->page_slug === $pageSlug && $section->key === $componentKey;
            $stored = $isCurrent ? ($section->settings ?? []) : [];
        @endphp
        @if (! empty($fields))
            <div class="section-settings-group" data-page="{{ $pageSlug }}" data-key="{{ $componentKey }}" style="display:none">
                @foreach ($fields as $name => $def)
                    @php
                        $current = old('settings.'.$name, $stored[$name] ?? null);
                    @endphp

                    @if ($def['type'] === 'text')
                        <div class="form-group">
                            <label class="form-label">{{ $def['label'] }}</label>
                            <input type="text" class="form-control" name="settings[{{ $name }}]" maxlength="255"
                                   value="{{ $current ?? '' }}" placeholder="{{ $def['default'] }}">
                            <small class="text-muted">Leave empty to use "{{ $def['default'] }}".</small>
                        </div>

                    @elseif ($def['type'] === 'link')
                        @php
                            $linkValue = $current ?? $def['default'];
                            $customValue = old('settings.'.$name.'_url', $stored[$name.'_url'] ?? '');
                        @endphp
                        <div class="form-group">
                            <label class="form-label">{{ $def['label'] }}</label>
                            <select class="form-select section-link-select" name="settings[{{ $name }}]">
                                @foreach (\App\Common\Services\SectionSettings::LINKS as $linkKey => $link)
                                    <option value="{{ $linkKey }}" @selected($linkValue === $linkKey)>{{ $link['label'] }}</option>
                                @endforeach
                            </select>
                            <input type="url" class="form-control mt-2 section-link-custom" name="settings[{{ $name }}_url]"
                                   value="{{ $customValue }}" placeholder="https://example.com/page"
                                   style="{{ $linkValue === 'custom' ? '' : 'display:none' }}">
                        </div>

                    @elseif ($def['type'] === 'toggle')
                        @php
                            $isOn = (string) ($current ?? $def['default']) === '1';
                        @endphp
                        <div class="form-group">
                            {{-- Hidden '0' first: PHP keeps the last value, so a ticked box ('1') wins. --}}
                            <input type="hidden" name="settings[{{ $name }}]" value="0">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="toggle-{{ $pageSlug }}-{{ $componentKey }}-{{ $name }}"
                                       name="settings[{{ $name }}]" value="1" @checked($isOn)>
                                <label class="form-check-label" for="toggle-{{ $pageSlug }}-{{ $componentKey }}-{{ $name }}">{{ $def['label'] }}</label>
                            </div>
                        </div>

                    @elseif ($def['type'] === 'color')
                        <div class="form-group">
                            <label class="form-label">{{ $def['label'] }}</label>
                            <select class="form-select" name="settings[{{ $name }}]">
                                @foreach (\App\Common\Services\SectionSettings::BACKGROUNDS as $bgKey => $bgLabel)
                                    <option value="{{ $bgKey }}" @selected(($current ?? $def['default']) === $bgKey)>{{ $bgLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    @endforeach
@endforeach
