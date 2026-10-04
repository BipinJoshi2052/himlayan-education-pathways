@extends('layouts.web')

@section('content')
    <section class="section-top">
        <div class="container">
            <div class="col-lg-10 offset-lg-1 text-center">
                <div class="section-top-title wow fadeInRight">
                    <h1>{{ __('web.gallery.title') }}</h1>
                    <ul>
                        <li><a href="{{ route('web.home') }}">{{ __('web.nav.home') }}</a></li>
                        <li> / {{ __('web.nav.gallery') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="blog_area section-padding">
        <div class="container">
            <div class="row g-4">
                @forelse ($galleries as $gallery)
                    <div class="col-lg-4 col-sm-6 col-xs-12 wow fadeInUp">
                        <div class="single_blog gallery-card">
                            <a href="{{ route('web.gallery.show', $gallery->slug) }}">
                                @if ($gallery->getFirstMediaUrl('cover'))
                                    <img src="{{ $gallery->getFirstMediaUrl('cover') }}" class="img-fluid" alt="{{ $gallery->title }}">
                                @else
                                    <img src="{{ asset('web-assets/img/blog/'.(($loop->index % 3) + 1).'.jpg') }}" class="img-fluid" alt="{{ $gallery->title }}">
                                @endif
                            </a>
                            <div class="content_box">
                                <span>{{ __('web.gallery.photos_count', ['count' => $gallery->getMedia('photos')->count()]) }}</span>
                                <h2><a href="{{ route('web.gallery.show', $gallery->slug) }}">{{ $gallery->title }}</a></h2>
                                @if ($gallery->description)
                                    <div class="gallery-summary">{!! $gallery->description !!}</div>
                                @endif
                                <a class="btn_one" href="{{ route('web.gallery.show', $gallery->slug) }}">{{ __('web.gallery.view_photos') }} <i class="ti-arrow-top-right"></i></a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p>{{ __('web.gallery.no_galleries') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
