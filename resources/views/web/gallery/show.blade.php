@extends('layouts.web')

@section('content')
    <section class="section-top">
        <div class="container">
            <div class="col-lg-10 offset-lg-1 text-center">
                <div class="section-top-title wow fadeInRight">
                    <h1>{{ $gallery->title }}</h1>
                    <ul>
                        <li><a href="{{ route('web.home') }}">Home</a></li>
                        <li><a href="{{ route('web.gallery.index') }}"> / Gallery</a></li>
                        <li> / {{ $gallery->title }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="section-padding">
        <div class="container">
            @if ($gallery->description)
                <div class="text-center mb-5">{!! $gallery->description !!}</div>
            @endif

            @if ($photos->isEmpty())
                <p class="text-center">No photos in this gallery yet.</p>
            @else
                <div class="row g-4 gallery-grid">
                    @foreach ($photos as $photo)
                        <div class="col-lg-4 col-sm-6 col-xs-12">
                            <figure class="gallery-photo">
                                <a href="{{ $photo->getUrl() }}" title="{{ $photo->getCustomProperty('caption') }}">
                                    <img src="{{ $photo->getUrl() }}" alt="{{ $photo->getCustomProperty('alt_text') ?: $gallery->title }}" loading="lazy">
                                </a>
                                @if ($photo->getCustomProperty('caption'))
                                    <figcaption>{{ $photo->getCustomProperty('caption') }}</figcaption>
                                @endif
                            </figure>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.jQuery && jQuery.fn.magnificPopup) {
                jQuery('.gallery-grid').magnificPopup({
                    delegate: 'a',
                    type: 'image',
                    gallery: { enabled: true },
                });
            }
        });
    </script>
@endpush
