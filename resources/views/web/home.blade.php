@extends('layouts.web')

@php
    $hero = $sections->get('hero');
    $stats = $sections->get('stats');
    $journey = $sections->get('journey');
    $about = $sections->get('about');
    $categories = $sections->get('popular-categories');
    $whyChooseUs = $sections->get('why-choose-us');
    $leadership = $sections->get('leadership');
    $testimonials = $sections->get('testimonials');
    $careerCta = $sections->get('career-cta');
@endphp

@section('content')

    @php
        // Hero is a Repeater: each item is one slide (own title/content/
        // image). One item renders as the plain hero below; 2+ wraps the
        // same markup in an owl-carousel — see docs/public-website.md.
        // Which of those two slide designs to use is an admin choice
        // (Website → Hero), independent of the slide content itself.
        $heroSlides = $hero?->items ?? collect();
        $heroLayout = \App\Models\Setting::get('hero_layout', 'classic');
    @endphp

    @if ($hero && $heroSlides->isNotEmpty())
        <!-- START HOME -->
        <section class="home_bg hb_height {{ \App\Common\Services\SectionSettings::backgroundClass($hero) }} @if ($heroLayout === 'full_image') hero-layout-full @endif @if ($heroSlides->count() > 1) hero-carousel-wrap @endif" style="background-image: url({{ asset('web-assets/img/bg/home-bg.jpg') }}); background-size:cover; background-position: center center;">
            <div class="container">
                <div class="{{ $heroSlides->count() > 1 ? 'hero-carousel owl-carousel' : '' }}">
                    @foreach ($heroSlides as $slide)
                        @if ($heroLayout === 'full_image')
                            <div class="hero-full" style="background-image: url('{{ $slide->getFirstMediaUrl('image', 'web') ?: asset('web-assets/img/bg/home-bg.jpg') }}');">
                                <div class="hero-full-overlay"></div>
                                <div class="hero-full-content">
                                    <h1>{{ $slide->title }}</h1>
                                    {!! $slide->description !!}
                                    <div class="home_sb">
                                        @if (\App\Common\Services\SectionSettings::enabled($hero, 'show_primary'))<a href="{{ \App\Common\Services\SectionSettings::url($hero, 'primary_link') }}" class="btn_one">{{ \App\Common\Services\SectionSettings::text($hero, 'primary_cta') }} <i class="ti-arrow-top-right"></i></a>@endif
                                        @if (\App\Common\Services\SectionSettings::enabled($hero, 'show_secondary'))<a href="{{ \App\Common\Services\SectionSettings::url($hero, 'secondary_link') }}" class="btn_one btn_two">{{ \App\Common\Services\SectionSettings::text($hero, 'secondary_cta') }}</a>@endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="row align-items-center">
                                <div class="col-lg-7 col-sm-12 col-xs-12">
                                    <div class="hero-text ht_top">
                                        <h1>{{ $slide->title }}</h1>
                                        {!! $slide->description !!}
                                    </div>
                                    <div class="home_sb">
                                        @if (\App\Common\Services\SectionSettings::enabled($hero, 'show_primary'))<a href="{{ \App\Common\Services\SectionSettings::url($hero, 'primary_link') }}" class="btn_one">{{ \App\Common\Services\SectionSettings::text($hero, 'primary_cta') }} <i class="ti-arrow-top-right"></i></a>@endif
                                        @if (\App\Common\Services\SectionSettings::enabled($hero, 'show_secondary'))<a href="{{ \App\Common\Services\SectionSettings::url($hero, 'secondary_link') }}" class="btn_one btn_two">{{ \App\Common\Services\SectionSettings::text($hero, 'secondary_cta') }}</a>@endif
                                    </div>
                                </div>
                                <div class="col-lg-5 col-sm-12 col-xs-12">
                                    <div class="hero-text-img">
                                        <img src="{{ $slide->getFirstMediaUrl('image', 'web') ?: asset('web-assets/img/home-img2.png') }}" class="img-fluid" alt="German language classes at {{ \App\Models\Setting::get('site_name', config('app.name')) }}" />
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
        <!-- END HOME -->

        @if ($heroSlides->count() > 1)
            @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        if (window.jQuery) {
                            jQuery('.hero-carousel').owlCarousel({
                                singleItem: true,
                                autoPlay: 3000,
                                stopOnHover: true,
                                navigation: false,
                                pagination: true,
                            });
                        }
                    });
                </script>
            @endpush
        @endif
    @endif

    @if ($stats && $stats->items->isNotEmpty())
        <!-- START COUNTER -->
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
        <!-- END COUNTER -->
    @endif

    @if ($journey)
        <!-- START JOURNEY -->
        <section class="top_cat__area section-padding" style="background-image: url({{ asset('web-assets/img/bg/shape-1.png') }}); background-size:cover; background-position: center center;">
            <div class="container">
                <div class="section-title text-center">
                    <h2>{{ $journey->title }}</h2>
                    {!! $journey->content !!}
                </div>
                <div class="row">
                    @foreach ($journey->items as $item)
                        <div class="col-lg-3 col-sm-6 col-xs-12 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.{{ $loop->iteration + 1 }}s">
                            <div class="single_tp journey-card">
                                <div class="journey-card-head">
                                    <span class="sc_one">{{ $item->icon_or_badge ?: sprintf('%02d', $loop->iteration) }}</span>
                                    <h3>{{ $item->title }}</h3>
                                </div>
                                {!! $item->description !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        <!-- END JOURNEY -->
    @endif

    @if ($about)
        <!-- START ABOUT US -->
        <section class="ab_area section-padding {{ \App\Common\Services\SectionSettings::backgroundClass($about) }}">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-sm-12 col-xs-12 wow fadeInUp">
                        <div class="ab_img">
                            <img src="{{ $about->getFirstMediaUrl('image', 'web') ?: asset('web-assets/img/about1.png') }}" class="img-fluid" alt="{{ $about->title }}">
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12 col-xs-12 wow fadeInUp">
                        <div class="ab_content">
                            <h2>{{ $about->title }}</h2>
                            {!! $about->content !!}
                            @if ($about->items->isNotEmpty())
                                <ul>
                                    @foreach ($about->items as $item)
                                        <li><span class="ti-check"></span> {{ $item->title }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            <a class="btn_one" href="{{ \App\Common\Services\SectionSettings::url($about, 'cta_link') }}">{{ \App\Common\Services\SectionSettings::text($about, 'cta') }} <i class="ti-arrow-top-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- END ABOUT US -->
    @endif

    @if ($categories && $categories->items->isNotEmpty())
        <!-- START CATEGORY -->
        <section class="top_cat__area section-padding" style="background-image: url({{ asset('web-assets/img/bg/section-2.jpg') }}); background-size:cover; background-position: center center;">
            <div class="container">
                <div class="section-title text-center">
                    <h2>{{ $categories->title }}</h2>
                    {!! $categories->content !!}
                </div>
                <div class="row">
                    <div class="col-lg-12 col-sm-12 col-xs-12 wow fadeInUp">
                        <div class="cat_list">
                            <ul>
                                @foreach ($services as $service)
                                    <li><a href="{{ route('web.courses.show', $service->slug) }}">{{ $service->title }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- END CATEGORY -->
    @endif

    {{-- Hidden for now: set to true to show the "German Language Courses in Kathmandu" block again. --}}
    @php $showHomeCourses = false; @endphp
    @if ($showHomeCourses && $services->isNotEmpty())
        <!-- START COURSE -->
        <section class="home_course section-padding">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-sm-6 col-xs-12">
                        <div class="section-title">
                            <h2>{{ __('web.home.courses_heading') }}</h2>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-xs-12">
                        <div class="cour_btn">
                            <a href="{{ route('web.courses.index') }}" class="btn_one">{{ __('web.home.view_all_courses') }} <i class="ti-arrow-top-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="row g-4">
                    @foreach ($services as $service)
                        <div class="col-lg-4 col-sm-6 col-xs-12">
                            <div class="single_course">
                                <div class="single_c_img">
                                    @if ($service->getFirstMediaUrl('cover_image', 'web'))
                                        <img src="{{ $service->getFirstMediaUrl('cover_image', 'web') }}" class="img-fluid" alt="{{ $service->title }}" />
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
                    @endforeach
                </div>
            </div>
        </section>
        <!-- END COURSE -->
    @endif

    @if ($leadership && $leadership->items->isNotEmpty())
        <!-- START TEAM -->
        <section class="team_area section-padding">
            <div class="container">
                <div class="section-title text-center">
                    <h2>{{ $leadership->title }}</h2>
                    {!! $leadership->content !!}
                </div>
                <div class="row justify-content-center">
                    @foreach ($leadership->items as $item)
                        <div class="col-lg-3 col-sm-6 col-xs-12 wow fadeInUp">
                            <div class="our-team">
                                <div class="team-content">
                                    <img src="{{ $item->getFirstMediaUrl('image', 'web') ?: asset('admin-assets/images/dummy-user.avif') }}" alt="{{ $item->title }}" class="{{ $item->getFirstMediaUrl('image', 'web') ? '' : 'dummy-photo' }}">
                                </div>
                                <div class="team-prof">
                                    <h3>{{ $item->title }}</h3>
                                    <span>{{ $item->icon_or_badge }}</span>
                                </div>
                                <div class="sth_det2">
                                    <div class="px-3 pb-2 mb-0 text-muted small">{!! $item->description !!}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        <!-- END TEAM -->
    @endif

    @if ($whyChooseUs)
        <!-- START WHY CHOOSE US -->
        <section class="ab_area section-padding {{ \App\Common\Services\SectionSettings::backgroundClass($whyChooseUs) }}">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-sm-12 col-xs-12 wow fadeInUp">
                        <div class="ab_content">
                            <h2>{{ $whyChooseUs->title }}</h2>
                            {!! $whyChooseUs->content !!}
                            @if ($whyChooseUs->items->isNotEmpty())
                                <ul>
                                    @foreach ($whyChooseUs->items as $item)
                                        {{-- Rich text produces block HTML (<p>...</p>); this list item
                                             needs a short inline line, so only safe inline tags survive. --}}
                                        <li><span class="ti-check"></span> <b>{{ $item->title }}:</b> {!! strip_tags($item->description, '<b><strong><i><em><a><br>') !!}</li>
                                    @endforeach
                                </ul>
                            @endif
                            @if (\App\Common\Services\SectionSettings::enabled($whyChooseUs, 'show_button'))
                                <a class="btn_one" href="{{ \App\Common\Services\SectionSettings::url($whyChooseUs, 'cta_link') }}">{{ \App\Common\Services\SectionSettings::text($whyChooseUs, 'cta') }} <i class="ti-arrow-top-right"></i></a>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12 col-xs-12 wow fadeInUp">
                        <div class="ab_img">
                            <img src="{{ $whyChooseUs->getFirstMediaUrl('image', 'web') ?: asset('web-assets/img/about3.png') }}" class="img-fluid" alt="{{ $whyChooseUs->title }}">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- END WHY CHOOSE US -->
    @endif

    @if ($testimonials && $testimonials->items->isNotEmpty())
        <!-- START TESTIMONIALS -->
        <section class="testi_area section-padding">
            <div class="container">
                <div class="section-title">
                    <h2>{{ __('web.home.students_say') }}</h2>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-sm-12 col-xs-12">
                        <div class="ab_img">
                            <img src="{{ $testimonials->getFirstMediaUrl('image', 'web') ?: asset('web-assets/img/review.png') }}" class="img-fluid" alt="{{ __('web.home.testimonial_alt') }}">
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12 col-xs-12">
                        <div id="testimonial-slider" class="owl-carousel">
                            @foreach ($testimonials->items as $item)
                                <div class="testimonial">
                                    <img src="{{ asset('web-assets/img/quote.png') }}" alt="" />
                                    <div class="testimonial_content">
                                        {!! $item->description !!}
                                    </div>
                                    <div class="testi_pic_title">
                                        <img src="{{ $item->getFirstMediaUrl('image', 'web') ?: asset('web-assets/img/testimonial/'.(($loop->index % 5) + 1).'.png') }}" alt="">
                                        <h4>{{ $item->title }}</h4>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- END TESTIMONIALS -->
    @endif

    @if ($posts->isNotEmpty())
        <!-- START BLOG -->
        <section id="blog" class="blog_area section-padding">
            <div class="container">
                <div class="section-title text-center">
                    <h2>{{ __('web.home.blog_heading') }}</h2>
                    <p>{{ __('web.home.blog_subtitle') }}</p>
                </div>
                <div class="row g-4">
                    @foreach ($posts as $post)
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
                                    <a class="btn_one" href="{{ route('web.blog.show', $post->slug) }}">{{ __('web.home.read_more') }} <i class="ti-arrow-top-right"></i></a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        <!-- END BLOG -->
    @endif

    @if ($careerCta)
        <!-- START CAREER CTA -->
        <section class="top_cat__area section-padding {{ \App\Common\Services\SectionSettings::backgroundClass($careerCta) }}" style="background-image: url({{ asset('web-assets/img/bg/shape-1.png') }}); background-size:cover; background-position: center center;">
            <div class="container">
                <div class="section-title text-center">
                    <h2>{{ $careerCta->title }}</h2>
                    {!! $careerCta->content !!}
                    <a class="btn_one" href="{{ \App\Common\Services\SectionSettings::url($careerCta, 'cta_link') }}">{{ \App\Common\Services\SectionSettings::text($careerCta, 'cta') }} <i class="ti-arrow-top-right"></i></a>
                </div>
            </div>
        </section>
        <!-- END CAREER CTA -->
    @endif


@endsection
