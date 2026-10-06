@extends('layouts.web')

@section('content')
    <section class="section-top">
        <div class="container">
            <div class="col-lg-10 offset-lg-1 text-center">
                <div class="section-top-title wow fadeInRight" data-wow-duration="1s" data-wow-delay="0.3s" data-wow-offset="0">
                    <h1>{{ __('web.errors.404_title') }}</h1>
                    <ul>
                        <li><a href="{{ route('web.home') }}">{{ __('web.nav.home') }}</a></li>
                        <li> / 404</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="zero_area section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-sm-12 col-xs-12 text-center">
                    <div class="error_page">
                        <img src="{{ asset('web-assets/img/404.svg') }}" class="img-fluid" alt="{{ __('web.errors.404_image_alt') }}" />
                        <h2>{{ __('web.errors.404_heading') }}</h2>
                        <p>{{ __('web.errors.404_text') }}</p>
                        <div class="home_btn">
                            <a href="{{ route('web.home') }}" class="btn_one">{{ __('web.errors.404_back_home') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
