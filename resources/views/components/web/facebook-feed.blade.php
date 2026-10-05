@props(['posts', 'pageUrl' => null])

{{-- Latest posts from the institute's Facebook Page, drawn as site cards so they
     take the site's width, fonts and colours. --}}
<div class="facebook-feed">
    <div class="facebook-feed-grid">
        @foreach ($posts as $post)
            <a class="facebook-feed-card" href="{{ $post['url'] ?? $pageUrl ?? '#' }}" target="_blank" rel="noopener">
                @if ($post['image'])
                    <div class="facebook-feed-image">
                        <img src="{{ $post['image'] }}" alt="" loading="lazy">
                    </div>
                @endif
                <div class="facebook-feed-body">
                    <span class="facebook-feed-date"><i class="fa-brands fa-facebook"></i> {{ $post['date'] }}</span>
                    @if ($post['message'])
                        <p>{{ $post['message'] }}</p>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
    @if ($pageUrl)
        <div class="text-center mt-4">
            <a class="btn_one" href="{{ $pageUrl }}" target="_blank" rel="noopener">Follow us on Facebook <i class="ti-arrow-top-right"></i></a>
        </div>
    @endif
</div>
