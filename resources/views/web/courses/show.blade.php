@extends('layouts.web')

@push('json_ld')
    <x-web.json-ld :data="\App\Common\Services\StructuredData::course($service)" />
    <x-web.json-ld :data="\App\Common\Services\StructuredData::breadcrumbs([
        [__('web.nav.home'), url('/')],
        [__('web.nav.courses'), route('web.courses.index')],
        [$service->title, null],
    ])" />
@endpush

@section('content')
    <section class="section-top">
        <div class="container">
            <div class="col-lg-10 offset-lg-1 text-center">
                <div class="section-top-title wow fadeInRight">
                    <h1>{{ $service->title }}</h1>
                    <ul>
                        <li><a href="{{ route('web.home') }}">{{ __('web.nav.home') }}</a></li>
                        <li><a href="{{ route('web.courses.index') }}"> / {{ __('web.nav.courses') }}</a></li>
                        <li> / {{ $service->title }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="our_event section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-sm-8 col-xs-12">
                    <div class="single_event_single">
                        @if ($service->getFirstMediaUrl('cover_image', 'web'))
                            <img alt="{{ $service->title }}" class="img-fluid mb-4 course-detail-image" src="{{ $service->getFirstMediaUrl('cover_image', 'web') }}" />
                        @endif
                        <div class="single_event_text_single">
                            <h2 class="h4">{{ $service->title }}</h2>
                            @if ($service->summary)
                                <p>{{ $service->summary }}</p>
                            @endif
                            <div class="course-details-content section-bg">
                                <div class="overview">
                                    {!! $service->description !!}
                                </div>
                            </div>

                            <x-web.share-buttons :url="url()->current()" :title="$service->title" />
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-4 col-xs-12">
                    <div class="course_features">
                        <h3>{{ __('web.courses.features') }}</h3>
                        <ul>
                            @if ($service->duration)
                                <li><i class="fa fa-calendar"></i> {{ __('web.courses.duration') }} <b>{{ $service->duration }}</b></li>
                            @endif
                            @if ($service->fee !== null)
                                <li><i class="fa fa-tag"></i> {{ __('web.courses.fee') }} <b>Rs {{ (int) round((float) $service->fee) }}</b></li>
                            @endif
                        </ul>
                    </div>
                    <div class="event_info_register">
                        <a class="btn_one" href="{{ route('web.contact') }}">{{ __('web.nav.enroll_now') }}</a>
                    </div>

                    @if ($relatedServices->isNotEmpty())
                        <div class="related_course">
                            <h3>{{ __('web.courses.other_courses') }}</h3>
                            @foreach ($relatedServices as $related)
                                <div class="single_rc">
                                    <h2 class="h4"><a href="{{ route('web.courses.show', $related->slug) }}">{{ $related->title }}</a></h2>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
