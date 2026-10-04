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
                            <img src="{{ $intro->getFirstMediaUrl('image') ?: asset('web-assets/img/about1.png') }}" class="img-fluid" alt="{{ $intro->title }}">
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
        <section class="top_cat__area section-padding" style="background-image: url({{ asset('web-assets/img/bg/section-2.jpg') }}); background-size:cover; background-position: center center;">
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
        <section class="count_area counter_feature">
            <div class="container">
                <div class="row">
                    @foreach ($stats->items as $item)
                        <div class="col-lg-3 col-sm-6 col-xs-12">
                            <div class="single-counter">
                                <h2 class="counter-num">{{ $item->title }}</h2>
                                {!! $item->description !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

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
