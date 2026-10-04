@extends('layouts.web')

@section('content')
    <section class="section-top">
        <div class="container">
            <div class="col-lg-10 offset-lg-1 text-center">
                <div class="section-top-title wow fadeInRight">
                    <h1>Frequently Asked Questions</h1>
                    <ul>
                        <li><a href="{{ route('web.home') }}">Home</a></li>
                        <li> / FAQ</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="faq_area section-padding">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9 col-sm-12 col-xs-12">
                    @if ($section && $section->items->isNotEmpty())
                        <div class="accordion" id="faqAccordion">
                            @foreach ($section->items as $item)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $loop->index }}">
                                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $loop->index }}"
                                                aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse{{ $loop->index }}">
                                            {{ $item->title }}
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $loop->index }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                         aria-labelledby="heading{{ $loop->index }}" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            {!! $item->description !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center">FAQs coming soon.</p>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
