@php
    // Settings > Appearance drives both the admin panel (see
    // layouts/admin.blade.php) and the public site here. The template's own
    // literal hex values (#525fe1 / #f26b65) were swapped for var(--site-*,
    // <original hex>) throughout style.css so they stay the true fallback
    // when no Setting is saved yet — see docs/public-website.md.
    $sitePrimaryColor = \App\Models\Setting::get('primary_color', '#525fe1');
    $siteSecondaryColor = \App\Models\Setting::get('secondary_color', '#f26b65');
    $siteLogo = \App\Models\Setting::get('site_logo');
    $siteLogoWeb = \App\Models\Setting::get('site_logo_web') ?: $siteLogo;
    $siteName = \App\Models\Setting::get('site_name', config('app.name'));
    // Settings > Loader — 'default' (built-in spinner), 'custom' (uploaded
    // image, separate from the site logo), or 'none' (skip the preloader
    // entirely) — see docs/admin-ui.md.
    $loaderMode = \App\Models\Setting::get('loader_mode', 'default');
    $loaderImage = $loaderMode === 'custom' ? \App\Models\Setting::get('loader_image') : null;
    // Site-wide popup notice — fetched here rather than per-controller so
    // every public page gets it automatically, same pattern as the above.
    $activeNotice = \App\Models\Notice::currentlyActive()->orderBy('sort_order')->first();
    $googleAnalyticsId = \App\Models\Setting::get('google_analytics_id');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="--site-primary: {{ $sitePrimaryColor }}; --site-secondary: {{ $siteSecondaryColor }};">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <x-web.seo-head :entity="$seoEntity ?? null" :title="$seoTitle ?? null" :description="$seoDescription ?? null" />

    @if ($googleAnalyticsId)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $googleAnalyticsId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', '{{ $googleAnalyticsId }}');
        </script>
    @endif

    @if ($siteLogo)
        <link rel="icon" href="{{ $siteLogo }}">
        <link rel="apple-touch-icon" href="{{ $siteLogo }}">
    @endif

    {{-- Connect early to the font and CDN hosts, so their files start sooner. --}}
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>

    {{-- Needed for the first paint: Bootstrap and the template's base styles. --}}
    <link rel="stylesheet" href="{{ asset('web-assets/bootstrap/css/bootstrap.min.css') }}">
    {{-- DM Sans and Jost, served from this site (see docs/performance.md). --}}
    <link rel="stylesheet" href="{{ asset('web-assets/css/fonts.css') }}">

    {{-- Icons, carousel, popup and animation styles load after the first paint.
         media="print" with onload switches them to all media once loaded. --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons@7/css/flag-icons.min.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('web-assets/fonts/font-awesome.min.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('web-assets/fonts/themify-icons.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('web-assets/owlcarousel/css/owl.carousel.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('web-assets/owlcarousel/css/owl.theme.css') }}" media="print" onload="this.media='all'">
    {{-- Not deferred: this hides the phone menu list on desktop, so it must load with the page. --}}
    <link rel="stylesheet" href="{{ asset('web-assets/css/jquery-simple-mobilemenu.css') }}">
    <link rel="stylesheet" href="{{ asset('web-assets/css/magnific-popup.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('web-assets/css/animate.css') }}" media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons@7/css/flag-icons.min.css">
        <link rel="stylesheet" href="{{ asset('web-assets/fonts/font-awesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('web-assets/fonts/themify-icons.css') }}">
        <link rel="stylesheet" href="{{ asset('web-assets/owlcarousel/css/owl.carousel.css') }}">
        <link rel="stylesheet" href="{{ asset('web-assets/owlcarousel/css/owl.theme.css') }}">
        <link rel="stylesheet" href="{{ asset('web-assets/css/jquery-simple-mobilemenu.css') }}">
        <link rel="stylesheet" href="{{ asset('web-assets/css/magnific-popup.css') }}">
        <link rel="stylesheet" href="{{ asset('web-assets/css/animate.css') }}">
    </noscript>
    <link rel="stylesheet" href="{{ asset('web-assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('web-assets/css/web-overrides.css') }}?v={{ @filemtime(public_path('web-assets/css/web-overrides.css')) }}">
    <x-web.json-ld :data="\App\Common\Services\StructuredData::organization()" />
    @stack('json_ld')
    @stack('styles')
</head>

<body data-spy="scroll" data-offset="80">

    @if ($loaderMode !== 'none')
        <div class="preloaders">
            @if ($loaderImage)
                <img src="{{ $loaderImage }}" alt="{{ $siteName }}" class="preloader-logo">
            @else
                <span class="loader"></span>
            @endif
        </div>
    @endif

    @if ($activeNotice)
        @include('web.partials.notice-popup', ['notice' => $activeNotice])
    @endif

    @php
        $navServices = \App\Modules\Service\Models\Service::active()->orderBy('sort_order')->get();
    @endphp

    <!-- START NAVBAR -->
    <div id="navigation" class="navbar-light bg-faded site-navigation">
        <div class="container-fluid">
            <div class="row">
                <div class="col-20 align-self-center">
                    <div class="site-logo">
                        @if ($siteLogo)
                            <a href="{{ route('web.home') }}" class="navbar-brand-logo"><img src="{{ $siteLogoWeb }}" alt="{{ $siteName }}"></a>
                        @else
                            <a href="{{ route('web.home') }}" class="navbar-brand-text">{{ $siteName }}</a>
                        @endif
                    </div>
                </div>

                <div class="col-60 d-flex">
                    <nav id="main-menu">
                        <ul>
                            <li><a href="{{ route('web.home') }}">{{ __('web.nav.home') }}</a></li>
                            <li><a href="{{ route('web.about') }}">{{ __('web.nav.about') }}</a></li>
                            <li class="menu-item-has-children"><a href="{{ route('web.courses.index') }}">{{ __('web.nav.courses') }}</a>
                                <ul>
                                    @foreach ($navServices as $navService)
                                        <li><a href="{{ route('web.courses.show', $navService->slug) }}">{{ $navService->title }}</a></li>
                                    @endforeach
                                </ul>
                            </li>
                            <li><a href="{{ route('web.faq') }}">{{ __('web.nav.faq') }}</a></li>
                            <li><a href="{{ route('web.blog.index') }}">{{ __('web.nav.blog') }}</a></li>
<li><a href="{{ route('web.gallery.index') }}">{{ __('web.nav.gallery') }}</a></li>
                            <li><a href="{{ route('web.contact') }}">{{ __('web.nav.contact') }}</a></li>
                        </ul>
                    </nav>
                </div>

                <div class="col-20 d-none d-xl-flex align-items-center justify-content-end gap-2">
                    @php $webLocales = \App\Common\Services\LocaleOptions::forWeb(); @endphp
                    @if (count($webLocales) > 1)
                        <div class="locale-switcher dropdown">
                            <button type="button" class="locale-switcher-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                @if ($flag = \App\Common\Services\LocaleOptions::flagCode(app()->getLocale()))
                                    <span class="fi fi-{{ $flag }} me-1"></span>
                                @endif
                                {{ strtoupper(app()->getLocale()) }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @foreach ($webLocales as $localeCode => $localeLabel)
                                    <li>
                                        <a class="dropdown-item {{ app()->getLocale() === $localeCode ? 'active' : '' }}"
                                           href="{{ route('web.locale.switch', $localeCode) }}">
                                            @if ($flag = \App\Common\Services\LocaleOptions::flagCode($localeCode))
                                                <span class="fi fi-{{ $flag }} me-1"></span>
                                            @endif
                                            {{ $localeLabel }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <a href="{{ route('web.contact') }}" class="btn_one">{{ __('web.nav.enroll_now') }}</a>
                </div>

                <ul class="mobile_menu">
                    <li><a href="{{ route('web.home') }}">{{ __('web.nav.home') }}</a></li>
                    <li><a href="{{ route('web.about') }}">{{ __('web.nav.about') }}</a></li>
                    <li><a href="#">{{ __('web.nav.courses') }}</a>
                        <ul class="sub-menu">
                            @foreach ($navServices as $navService)
                                <li><a href="{{ route('web.courses.show', $navService->slug) }}">{{ $navService->title }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    <li><a href="{{ route('web.faq') }}">{{ __('web.nav.faq') }}</a></li>
                    <li><a href="{{ route('web.blog.index') }}">{{ __('web.nav.blog') }}</a></li>
<li><a href="{{ route('web.gallery.index') }}">{{ __('web.nav.gallery') }}</a></li>
                    <li><a href="{{ route('web.contact') }}">{{ __('web.nav.contact') }}</a></li>
                    @php $mobileLocales = \App\Common\Services\LocaleOptions::forWeb(); @endphp
                    @if (count($mobileLocales) > 1)
                        <li><a href="#">{{ __('web.nav.language') }}: {{ strtoupper(app()->getLocale()) }}</a>
                            <ul class="sub-menu">
                                @foreach ($mobileLocales as $localeCode => $localeLabel)
                                    <li>
                                        <a href="{{ route('web.locale.switch', $localeCode) }}" class="{{ app()->getLocale() === $localeCode ? 'active' : '' }}">
                                            @if ($flag = \App\Common\Services\LocaleOptions::flagCode($localeCode))
                                                <span class="fi fi-{{ $flag }} me-1"></span>
                                            @endif
                                            {{ $localeLabel }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <!-- END NAVBAR -->

    @yield('content')

    <!-- START FOOTER -->
    <div class="footer section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-sm-6 col-xs-12">
                    <div class="single_footer">
                        @if ($siteLogo)
                            <a href="{{ route('web.home') }}" class="navbar-brand-logo"><img src="{{ $siteLogoWeb }}" alt="{{ $siteName }}"></a>
                        @else
                            <a href="{{ route('web.home') }}" class="navbar-brand-text">{{ $siteName }}</a>
                        @endif
                        <p>{{ \App\Models\Setting::get('site_tagline', __('web.footer.tagline')) }}</p>
                        <div class="social_profile">
                            <ul>
                                @if (\App\Models\Setting::get('social_facebook_url'))
                                    <li><a href="{{ \App\Models\Setting::get('social_facebook_url') }}" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook-f" aria-hidden="true"></i></a></li>
                                @endif
                                @if (\App\Models\Setting::get('social_instagram_url'))
                                    <li><a href="{{ \App\Models\Setting::get('social_instagram_url') }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa-brands fa-instagram" aria-hidden="true"></i></a></li>
                                @endif
                                @if (\App\Models\Setting::get('social_tiktok_url'))
                                    <li><a href="{{ \App\Models\Setting::get('social_tiktok_url') }}" target="_blank" rel="noopener" aria-label="TikTok"><i class="fa-brands fa-tiktok" aria-hidden="true"></i></a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-6 col-xs-12">
                    <div class="single_footer">
                        <p class="footer-widget-title">{{ __('web.footer.quick_links') }}</p>
                        <ul aria-label="{{ __('web.footer.quick_links') }}">
                            <li><a href="{{ route('web.about') }}">{{ __('web.footer.about_us') }}</a></li>
                            <li><a href="{{ route('web.courses.index') }}">{{ __('web.footer.german_courses_link') }}</a></li>
                            <li><a href="{{ route('web.faq') }}">{{ __('web.footer.faq') }}</a></li>
                            <li><a href="{{ route('web.blog.index') }}">{{ __('web.footer.blog') }}</a></li>
<li><a href="{{ route('web.gallery.index') }}">{{ __('web.footer.gallery') }}</a></li>
                            <li><a href="{{ route('web.contact') }}">{{ __('web.footer.contact_us') }}</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-6 col-xs-12">
                    <div class="single_footer">
                        <p class="footer-widget-title">{{ __('web.footer.german_courses_heading') }}</p>
                        <ul aria-label="{{ __('web.footer.german_courses_heading') }}">
                            @foreach ($navServices as $navService)
                                <li><a href="{{ route('web.courses.show', $navService->slug) }}">{{ $navService->title }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6 col-xs-12">
                    <div class="single_footer">
                        <p class="footer-widget-title">{{ __('web.footer.contact_info') }}</p>
                        <div class="sf_contact">
                            <span class="ti-map"></span>
                            <p>
                                <a href="{{ \App\Common\Services\MapLink::googleUrl(\App\Models\Setting::get('site_address', 'Chabahil–7, Kathmandu, Nepal')) }}" target="_blank" rel="noopener">
                                    {{ \App\Models\Setting::get('site_address', 'Chabahil–7, Kathmandu, Nepal') }}
                                </a>
                            </p>
                        </div>
                        @if (\App\Models\Setting::get('contact_phone'))
                            <div class="sf_contact">
                                <span class="ti-mobile"></span>
                                <p>@foreach (\App\Models\Setting::phoneNumbers() as $phone)<a href="tel:{{ $phone['tel'] }}">{{ $phone['label'] }}</a>{{ $loop->last ? '' : ', ' }}@endforeach</p>
                            </div>
                        @endif
                        @if (\App\Models\Setting::get('contact_email'))
                            <div class="sf_contact">
                                <span class="ti-email"></span>
                                <p><x-web.email-link :email="\App\Models\Setting::get('contact_email')" /></p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END FOOTER -->

    <div class="foot_copy">
        <div class="container foot-copy-row">
            <p class="foot-copy-left">&copy; {{ date('Y') }} {{ $siteName }}. {{ __('web.footer.copyright') }}
                <a href="{{ route('web.privacy') }}">Privacy Policy</a> &middot; <a href="{{ route('web.terms') }}">Terms of Use</a>
            </p>
            <p class="foot-copy-right">Designed By <a href="https://joshibipin.com.np/" target="_blank" rel="noopener">Bipin Joshi</a></p>
        </div>
    </div>

    @php
        $whatsappPhone = \App\Models\Setting::get('whatsapp_enabled', false) ? \App\Models\Setting::get('whatsapp_phone') : null;
    @endphp
    @if ($whatsappPhone)
        <a href="https://wa.me/{{ preg_replace('/[^\d]/', '', $whatsappPhone) }}" target="_blank" rel="noopener"
           class="whatsapp-float" aria-label="Chat on WhatsApp">
            <i class="fa-brands fa-whatsapp"></i>
        </a>
    @endif

    <script src="{{ asset('web-assets/js/jquery-1.12.4.min.js') }}"></script>
    {{-- bootstrap.min.js (not the bundle variant) needs Popper for dropdowns
         to position/toggle at all — without it, data-bs-toggle="dropdown"
         silently does nothing. No Popper ships in web-assets, so it's
         loaded from CDN, same pattern as the admin layout's CDN libs. --}}
    <script defer src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script defer src="{{ asset('web-assets/bootstrap/js/bootstrap.min.js') }}"></script>
    <script defer src="{{ asset('web-assets/js/modernizr-2.8.3.min.js') }}"></script>
    <script defer src="{{ asset('web-assets/js/jquery-simple-mobilemenu.js') }}"></script>
    <script defer src="{{ asset('web-assets/owlcarousel/js/owl.carousel.min.js') }}"></script>
    <script defer src="{{ asset('web-assets/js/jquery.magnific-popup.min.js') }}"></script>
    <script defer src="{{ asset('web-assets/js/jquery.inview.min.js') }}"></script>
    <script defer src="{{ asset('web-assets/js/scrolltopcontrol.js') }}"></script>
    <script defer src="{{ asset('web-assets/js/wow.min.js') }}"></script>
    <script defer src="{{ asset('web-assets/js/scripts.js') }}"></script>
    @stack('scripts')
</body>

</html>
