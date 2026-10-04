{{--
    Usage: @include('admin.partials.locale-tabs', ['locales' => $locales])
    One switcher per form, toggling every [data-locale-pane] on the page
    together (title inputs, summary, rich-text editors) — see
    public/admin-assets/js/locale-tabs.js.
--}}
@if (count($locales) > 1)
    <ul class="nav nav-tabs mb-3" data-locale-switcher>
        @foreach ($locales as $i => $locale)
            <li class="nav-item">
                <button type="button" class="nav-link locale-switch-btn {{ $i === 0 ? 'active' : '' }}" data-locale="{{ $locale }}">
                    @if ($flag = \App\Common\Services\LocaleOptions::flagCode($locale))
                        <span class="fi fi-{{ $flag }} me-1"></span>
                    @endif
                    {{ config('app.available_locales')[$locale] ?? strtoupper($locale) }}
                </button>
            </li>
        @endforeach
    </ul>
@endif
