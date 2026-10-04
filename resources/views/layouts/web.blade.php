@php
    // Settings > Appearance drives both the admin panel (see
    // layouts/admin.blade.php) and the public site here. The template's own
    // literal hex values (#525fe1 / #f26b65) were swapped for var(--site-*,
    // <original hex>) throughout style.css so they stay the true fallback
    // when no Setting is saved yet — see docs/public-website.md.
    $sitePrimaryColor = \App\Models\Setting::get('primary_color', '#525fe1');
    $siteSecondaryColor = \App\Models\Setting::get('secondary_color', '#f26b65');
    $siteLogo = \App\Models\Setting::get('site_logo');
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

    <x-web.seo-head :entity="$seoEntity ?? null" />

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

    <link rel="stylesheet" href="{{ asset('web-assets/bootstrap/css/bootstrap.min.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons@7/css/flag-icons.min.css">
    <link rel="stylesheet" href="{{ asset('web-assets/fonts/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('web-assets/fonts/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('web-assets/owlcarousel/css/owl.carousel.css') }}">
    <link rel="stylesheet" href="{{ asset('web-assets/owlcarousel/css/owl.theme.css') }}">
    <link rel="stylesheet" href="{{ asset('web-assets/css/jquery-simple-mobilemenu.css') }}">
    <link rel="stylesheet" href="{{ asset('web-assets/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('web-assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('web-assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('web-assets/css/web-overrides.css') }}">
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
                            <a href="{{ route('web.home') }}" class="navbar-brand-logo"><img src="{{ $siteLogo }}" alt="{{ $siteName }}"></a>
                        @else
                            <a href="{{ route('web.home') }}" class="navbar-brand-text">{{ $siteName }}</a>
                        @endif
                    </div>
                </div>

                <div class="col-60 d-flex">
                    <nav id="main-menu">
                        <ul>
                            <li><a href="{{ route('web.home') }}">Home</a></li>
                            <li><a href="{{ route('web.about') }}">About</a></li>
                            <li class="menu-item-has-children"><a href="{{ route('web.courses.index') }}">Courses</a>
                                <ul>
                                    @foreach ($navServices as $navService)
                                        <li><a href="{{ route('web.courses.show', $navService->slug) }}">{{ $navService->title }}</a></li>
                                    @endforeach
                                </ul>
                            </li>
                            <li><a href="{{ route('web.faq') }}">FAQ</a></li>
                            <li><a href="{{ route('web.blog.index') }}">Blog</a></li>
<li><a href="{{ route('web.gallery.index') }}">Gallery</a></li>
                            <li><a href="{{ route('web.contact') }}">Contact</a></li>
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
                    <a href="{{ route('web.contact') }}" class="btn_one">Enroll Now</a>
                </div>

                <ul class="mobile_menu">
                    <li><a href="{{ route('web.home') }}">Home</a></li>
                    <li><a href="{{ route('web.about') }}">About</a></li>
                    <li><a href="#">Courses</a>
                        <ul class="sub-menu">
                            @foreach ($navServices as $navService)
                                <li><a href="{{ route('web.courses.show', $navService->slug) }}">{{ $navService->title }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    <li><a href="{{ route('web.faq') }}">FAQ</a></li>
                    <li><a href="{{ route('web.blog.index') }}">Blog</a></li>
<li><a href="{{ route('web.gallery.index') }}">Gallery</a></li>
                    <li><a href="{{ route('web.contact') }}">Contact</a></li>
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
                            <a href="{{ route('web.home') }}" class="navbar-brand-logo"><img src="{{ $siteLogo }}" alt="{{ $siteName }}"></a>
                        @else
                            <a href="{{ route('web.home') }}" class="navbar-brand-text">{{ $siteName }}</a>
                        @endif
                        <p>{{ \App\Models\Setting::get('site_tagline', 'Structured German language classes from A1 to B2 in Chabahil, Kathmandu.') }}</p>
                        <div class="social_profile">
                            <ul>
                                @if (\App\Models\Setting::get('social_facebook_url'))
                                    <li><a href="{{ \App\Models\Setting::get('social_facebook_url') }}" target="_blank" rel="noopener"><i class="fa-brands fa-facebook-f"></i></a></li>
                                @endif
                                @if (\App\Models\Setting::get('social_instagram_url'))
                                    <li><a href="{{ \App\Models\Setting::get('social_instagram_url') }}" target="_blank" rel="noopener"><i class="fa-brands fa-instagram"></i></a></li>
                                @endif
                                @if (\App\Models\Setting::get('social_tiktok_url'))
                                    <li><a href="{{ \App\Models\Setting::get('social_tiktok_url') }}" target="_blank" rel="noopener"><i class="fa-brands fa-tiktok"></i></a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-6 col-xs-12">
                    <div class="single_footer">
                        <h4>Quick Links</h4>
                        <ul>
                            <li><a href="{{ route('web.about') }}">About Us</a></li>
                            <li><a href="{{ route('web.courses.index') }}">German Courses</a></li>
                            <li><a href="{{ route('web.faq') }}">FAQ</a></li>
                            <li><a href="{{ route('web.blog.index') }}">Blog</a></li>
<li><a href="{{ route('web.gallery.index') }}">Gallery</a></li>
                            <li><a href="{{ route('web.contact') }}">Contact Us</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-6 col-xs-12">
                    <div class="single_footer">
                        <h4>German Courses</h4>
                        <ul>
                            @foreach ($navServices as $navService)
                                <li><a href="{{ route('web.courses.show', $navService->slug) }}">{{ $navService->title }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6 col-xs-12">
                    <div class="single_footer">
                        <h4>Contact Info</h4>
                        <div class="sf_contact">
                            <span class="ti-map"></span>
                            <p>
                                <a href="{{ \App\Common\Services\MapLink::pointUrl() ?? ('https://www.google.com/maps/search/?api=1&query=' . urlencode(\App\Models\Setting::get('site_address', 'Chabahil–7, Kathmandu, Nepal'))) }}" target="_blank" rel="noopener">
                                    {{ \App\Models\Setting::get('site_address', 'Chabahil–7, Kathmandu, Nepal') }}
                                </a>
                            </p>
                        </div>
                        @if (\App\Models\Setting::get('contact_phone'))
                            <div class="sf_contact">
                                <span class="ti-mobile"></span>
                                <p><a href="tel:{{ preg_replace('/[^\d+]/', '', \App\Models\Setting::get('contact_phone')) }}">{{ \App\Models\Setting::get('contact_phone') }}</a></p>
                            </div>
                        @endif
                        @if (\App\Models\Setting::get('contact_email'))
                            <div class="sf_contact">
                                <span class="ti-email"></span>
                                <p><a href="mailto:{{ \App\Models\Setting::get('contact_email') }}">{{ \App\Models\Setting::get('contact_email') }}</a></p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END FOOTER -->

    <div class="foot_copy">
        <div class="footer_copyright">
            <p>&copy; {{ date('Y') }} {{ $siteName }}. All Rights Reserved.</p>
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
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="{{ asset('web-assets/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('web-assets/js/modernizr-2.8.3.min.js') }}"></script>
    <script src="{{ asset('web-assets/js/jquery-simple-mobilemenu.js') }}"></script>
    <script src="{{ asset('web-assets/owlcarousel/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('web-assets/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('web-assets/js/jquery.inview.min.js') }}"></script>
    <script src="{{ asset('web-assets/js/scrolltopcontrol.js') }}"></script>
    <script src="{{ asset('web-assets/js/wow.min.js') }}"></script>
    <script src="{{ asset('web-assets/js/scripts.js') }}"></script>
    @stack('scripts')
</body>

</html>
