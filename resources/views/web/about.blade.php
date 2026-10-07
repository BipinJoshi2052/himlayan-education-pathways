@extends('layouts.web')

@php
    $intro = $sections->get('about-intro');
    $missionVision = $sections->get('about-mission-vision');
    $approach = $sections->get('about-approach');
    $stats = $sections->get('about-stats');
    $careerCta = $sections->get('career-cta');
@endphp

@section('content')
    <section class="section-top">
        <div class="container">
            <div class="col-lg-10 offset-lg-1 text-center">
                <div class="section-top-title wow fadeInRight">
                    <h1>{{ __('web.about.title') }}</h1>
                    <ul>
                        <li><a href="{{ route('web.home') }}">{{ __('web.nav.home') }}</a></li>
                        <li> / {{ __('web.nav.about') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    @if ($intro)
        <section class="ab_area section-padding {{ \App\Common\Services\SectionSettings::backgroundClass($intro) }}">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-sm-12 col-xs-12 wow fadeInUp">
                        <div class="ab_img">
                            <img src="{{ $intro->getFirstMediaUrl('image', 'web') ?: asset('web-assets/img/about1.webp') }}" class="img-fluid" alt="{{ $intro->title }}">
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12 col-xs-12 wow fadeInUp">
                        <div class="ab_content">
                            <h2>{{ $intro->title }}</h2>
                            {!! $intro->content !!}
                            @if ($intro->items->isNotEmpty())
                                <ul>
                                    @foreach ($intro->items as $item)
                                        <li><span class="ti-check"></span> {{ $item->title }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if ($missionVision && $missionVision->items->isNotEmpty())
        <section class="top_cat__area section-padding" style="background-image: url({{ asset('web-assets/img/bg/section-2.webp') }}); background-size:cover; background-position: center center;">
            <div class="container">
                <div class="row justify-content-center">
                    @foreach ($missionVision->items as $item)
                        <div class="col-lg-5 col-sm-6 col-xs-12 wow fadeInUp">
                            <div class="single_tp" style="background: #fff;">
                                <h3>{{ $item->title }}</h3>
                                {!! $item->description !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($approach && $approach->items->isNotEmpty())
        <section class="top_cat__area section-padding" style="background-image: url({{ asset('web-assets/img/bg/shape-1.png') }}); background-size:cover; background-position: center center;">
            <div class="container">
                <div class="section-title text-center">
                    <h2>{{ $approach->title }}</h2>
                    {!! $approach->content !!}
                </div>
                <div class="row">
                    @foreach ($approach->items as $item)
                        <div class="col-lg-3 col-sm-6 col-xs-12 wow fadeInUp">
                            <div class="single_tp">
                                <h3>{{ $item->title }}</h3>
                                {!! $item->description !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($stats && $stats->items->isNotEmpty())
        <section class="count_area counter_feature about-stats-section">
            <div class="container">
                <div class="row">
                    @foreach ($stats->items as $item)
                        <div class="col-lg-3 col-sm-6 col-xs-12">
                            <div class="single-counter">
                                <p class="counter-num">{{ $item->title }}</p>
                                {!! $item->description !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Facebook and TikTok load after the page has finished loading, from their own
         endpoints, so the page itself never waits on those services. Each section
         stays hidden until its content arrives. --}}
    <section class="section-padding social-feed-section" id="facebook-section" style="display:none">
        <div class="container">
            <div class="section-title text-center">
                <h2>Follow Our Journey on Facebook</h2>
                <p>The latest from our Facebook page.</p>
            </div>
            <div class="social-feed-slot" data-feed="{{ route('social.facebook') }}"></div>
        </div>
    </section>

    <section class="section-padding social-feed-section" id="tiktok-section" style="display:none">
        <div class="container">
            <div class="section-title text-center">
                <h2>Watch Us on TikTok</h2>
                <p>Short German lessons and student moments.</p>
            </div>
            <div class="social-feed-slot" data-feed="{{ route('social.tiktok') }}"></div>
        </div>
    </section>

    <div id="fb-root"></div>

    @push('scripts')
        <script>
            (function () {
                // Fetch each section's HTML once the page has loaded, then wire up its popups.
                function fillSlot(slot, onFilled) {
                    fetch(slot.dataset.feed, { credentials: 'same-origin', headers: { Accept: 'text/html' } })
                        .then(function (response) { return response.ok ? response.text() : ''; })
                        .then(function (html) {
                            if (html.trim() === '') {
                                return;
                            }
                            slot.innerHTML = html;
                            slot.closest('.social-feed-section').style.display = '';
                            onFilled(slot);
                        })
                        .catch(function () { /* The section stays hidden. */ });
                }

                function loadFacebookSdk() {
                    if (window.FB || document.getElementById('fb-sdk')) {
                        return;
                    }
                    var script = document.createElement('script');
                    script.id = 'fb-sdk';
                    script.async = true;
                    script.crossOrigin = 'anonymous';
                    script.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v20.0';
                    document.body.appendChild(script);
                }

                function wireFacebook(slot) {
                    slot.querySelectorAll('.facebook-post-modal').forEach(function (modal) {
                        var media = modal.querySelector('.facebook-post-media');
                        if (!media) {
                            return;
                        }
                        var original = media.innerHTML;

                        modal.addEventListener('shown.bs.modal', function () {
                            var video = media.querySelector('.fb-video');
                            if (!video || !window.FB) {
                                return;
                            }
                            // Size the player so the whole video fits on screen; its shape
                            // comes from the card thumbnail.
                            var thumb = document.querySelector('[data-bs-target="#' + modal.id + '"] img');
                            var ratio = thumb && thumb.naturalWidth && thumb.naturalHeight
                                ? thumb.naturalWidth / thumb.naturalHeight
                                : 9 / 16;
                            var width = Math.floor(Math.min(media.clientWidth || 400, window.innerHeight * 0.85 * ratio));
                            video.setAttribute('data-width', String(width));
                            FB.XFBML.parse(media);
                        });

                        // Closing resets the media, so a video stops and is parsed again on reopen.
                        modal.addEventListener('hidden.bs.modal', function () {
                            media.innerHTML = original;
                        });
                    });

                    if (slot.querySelector('.fb-video')) {
                        loadFacebookSdk();
                    }
                }

                function wireTikTok(slot) {
                    slot.querySelectorAll('.tiktok-video-modal').forEach(function (modal) {
                        var frame = modal.querySelector('.tiktok-video-frame');
                        if (!frame) {
                            return;
                        }
                        modal.addEventListener('shown.bs.modal', function () {
                            if (!frame.getAttribute('src')) {
                                frame.setAttribute('src', frame.dataset.src);
                            }
                        });
                        // Removing the address stops playback and frees the player.
                        modal.addEventListener('hidden.bs.modal', function () {
                            frame.removeAttribute('src');
                        });
                    });
                }

                function start() {
                    document.querySelectorAll('.social-feed-slot').forEach(function (slot) {
                        var isFacebook = slot.dataset.feed.indexOf('/social/facebook') !== -1;
                        fillSlot(slot, isFacebook ? wireFacebook : wireTikTok);
                    });
                }

                if (document.readyState === 'complete') {
                    start();
                } else {
                    window.addEventListener('load', start);
                }
            })();
        </script>
    @endpush

    @if ($careerCta)
        <section class="top_cat__area section-padding {{ \App\Common\Services\SectionSettings::backgroundClass($careerCta) }}" style="background-image: url({{ asset('web-assets/img/bg/shape-1.png') }}); background-size:cover; background-position: center center;">
            <div class="container">
                <div class="section-title text-center">
                    <h2>{{ $careerCta->title }}</h2>
                    {!! $careerCta->content !!}
                    <a class="btn_one" href="{{ \App\Common\Services\SectionSettings::url($careerCta, 'cta_link') }}">{{ \App\Common\Services\SectionSettings::text($careerCta, 'cta') }} <i class="ti-arrow-top-right"></i></a>
                </div>
            </div>
        </section>
    @endif
@endsection
