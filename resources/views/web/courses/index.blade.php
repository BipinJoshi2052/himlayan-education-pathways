@extends('layouts.web')

@section('content')
    <section class="section-top">
        <div class="container">
            <div class="col-lg-10 offset-lg-1 text-center">
                <div class="section-top-title wow fadeInRight">
                    <h1>{{ __('web.courses.title') }}</h1>
                    <ul>
                        <li><a href="{{ route('web.home') }}">{{ __('web.nav.home') }}</a></li>
                        <li> / {{ __('web.nav.courses') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="home_course section-padding">
        <div class="container">
            <div class="row g-4">
                @forelse ($services as $service)
                    <div class="col-lg-4 col-sm-6 col-xs-12">
                        <div class="single_course">
                            <div class="single_c_img">
                                @if ($service->getFirstMediaUrl('cover_image'))
                                    <img src="{{ $service->getFirstMediaUrl('cover_image') }}" class="img-fluid" alt="{{ $service->title }}" />
                                @else
                                    <img src="{{ asset('web-assets/img/course/'.(($loop->index % 6) + 1).'.png') }}" class="img-fluid" alt="{{ $service->title }}" />
                                @endif
                            </div>
                            <h4><a href="{{ route('web.courses.show', $service->slug) }}">{{ $service->title }}</a></h4>
                            <div class="single-course-body">
                                <p>{{ $service->summary }}</p>
                                @if ($service->duration)
                                    <p><span class="ti-alarm-clock"></span> {{ $service->duration }}</p>
                                @endif
                                <a class="btn_one" href="{{ route('web.courses.show', $service->slug) }}">{{ __('web.home.view_course') }} <i class="ti-arrow-top-right"></i></a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p>{{ __('web.courses.coming_soon') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
