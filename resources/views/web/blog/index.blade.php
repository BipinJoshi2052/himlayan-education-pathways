@extends('layouts.web')

@section('content')
    <section class="section-top">
        <div class="container">
            <div class="col-lg-10 offset-lg-1 text-center">
                <div class="section-top-title wow fadeInRight">
                    <h1>German Language Learning Blog</h1>
                    <ul>
                        <li><a href="{{ route('web.home') }}">Home</a></li>
                        <li> / Blog</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section id="blog" class="blog_area section-padding">
        <div class="container">
            <div class="row g-4" id="posts-container" data-next-page-url="{{ $posts->nextPageUrl() ?? '' }}">
                @forelse ($posts as $post)
                    <div class="col-lg-4 col-sm-4 col-xs-12 wow fadeInUp">
                        <div class="single_blog">
                            @if ($post->getFirstMediaUrl('featured_image', 'medium'))
                                <img src="{{ $post->getFirstMediaUrl('featured_image', 'medium') }}" class="img-fluid" alt="{{ $post->title }}" />
                            @else
                                <img src="{{ asset('web-assets/img/blog/'.(($loop->index % 3) + 1).'.jpg') }}" class="img-fluid" alt="{{ $post->title }}" />
                            @endif
                            <div class="content_box">
                                <span>{{ $post->published_at?->format('M d, Y') ?? $post->created_at->format('M d, Y') }}</span>
                                <h2><a href="{{ route('web.blog.show', $post->slug) }}">{{ $post->title }}</a></h2>
                                <p>{{ $post->summary }}</p>
                                <a class="btn_one" href="{{ route('web.blog.show', $post->slug) }}">Read More <i class="ti-arrow-top-right"></i></a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p>No posts published yet.</p>
                    </div>
                @endforelse
            </div>

            @if ($posts->hasMorePages())
                <div id="posts-infinite-sentinel" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading more posts...</span>
                    </div>
                </div>
            @endif
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var container = document.getElementById('posts-container');
                var sentinel = document.getElementById('posts-infinite-sentinel');
                if (!container || !sentinel) {
                    return;
                }

                var nextUrl = container.dataset.nextPageUrl || null;
                var loading = false;

                // Re-fetches the SAME blog index route (just with ?page=N) and
                // pulls the new cards out of the returned full page — no
                // separate JSON/API endpoint needed, same controller serves
                // both the normal and the infinite-scroll requests.
                function loadMore() {
                    if (!nextUrl || loading) {
                        return;
                    }
                    loading = true;

                    fetch(nextUrl)
                        .then(function (response) { return response.text(); })
                        .then(function (html) {
                            var doc = new DOMParser().parseFromString(html, 'text/html');
                            var newContainer = doc.getElementById('posts-container');

                            if (newContainer) {
                                container.insertAdjacentHTML('beforeend', newContainer.innerHTML);
                                nextUrl = newContainer.dataset.nextPageUrl || null;
                            } else {
                                nextUrl = null;
                            }

                            loading = false;

                            if (!nextUrl) {
                                observer.disconnect();
                                sentinel.remove();
                            }
                        })
                        .catch(function () {
                            loading = false;
                        });
                }

                var observer = new IntersectionObserver(function (entries) {
                    if (entries[0].isIntersecting) {
                        loadMore();
                    }
                });
                observer.observe(sentinel);
            });
        </script>
    @endpush
@endsection
