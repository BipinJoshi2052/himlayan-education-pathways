@extends('layouts.web')

@push('json_ld')
    <x-web.json-ld :data="\App\Common\Services\StructuredData::blogPosting($post)" />
    <x-web.json-ld :data="\App\Common\Services\StructuredData::breadcrumbs([
        [__('web.nav.home'), url('/')],
        [__('web.nav.blog'), route('web.blog.index')],
        [$post->title, null],
    ])" />
@endpush

@section('content')
    <section class="section-top">
        <div class="container">
            <div class="col-lg-10 offset-lg-1 text-center">
                <div class="section-top-title wow fadeInRight">
                    <h1>{{ $post->title }}</h1>
                    <ul>
                        <li><a href="{{ route('web.home') }}">{{ __('web.nav.home') }}</a></li>
                        <li><a href="{{ route('web.blog.index') }}"> / {{ __('web.nav.blog') }}</a></li>
                        <li> / {{ $post->title }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="blog-page section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-sm-8 col-xs-12">
                    @if ($post->getFirstMediaUrl('featured_image', 'medium'))
                        <img src="{{ $post->getFirstMediaUrl('featured_image', 'medium') }}" class="img-fluid mb-4" alt="{{ $post->title }}">
                    @endif
                    <span class="text-muted d-block mb-3">{{ $post->published_at?->format('M d, Y') ?? $post->created_at->format('M d, Y') }}</span>
                    <div class="blog-content">
                        {!! $post->content !!}
                    </div>

                    <x-web.share-buttons :url="url()->current()" :title="$post->title" />
                </div>

                <div class="col-lg-4 col-sm-4 col-xs-12">
                    @if ($recentPosts->isNotEmpty())
                        <div class="sidebar-post mb-4">
                            <div class="sidebar_title"><h2 class="sidebar-widget-title">{{ __('web.blog.popular_posts') }}</h2></div>
                            <ul class="list-unstyled">
                                @foreach ($recentPosts as $recent)
                                    <li class="mb-2"><a href="{{ route('web.blog.show', $recent->slug) }}">{{ $recent->title }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
