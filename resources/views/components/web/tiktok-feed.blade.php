@props(['videos', 'pageUrl' => null])

{{-- TikTok videos as thumbnail cards. A card opens a popup with the TikTok player
     on the left and the title on the right. The player's address is set only when
     the popup opens, and cleared when it closes, so nothing plays until clicked. --}}
<div class="tiktok-feed">
    <div class="tiktok-feed-grid">
        @foreach ($videos as $video)
            <button type="button" class="tiktok-feed-card" data-bs-toggle="modal" data-bs-target="#tt-video-{{ $loop->index }}">
                <div class="tiktok-feed-thumb">
                    @if ($video['thumbnail'])
                        <img src="{{ $video['thumbnail'] }}" alt="" loading="lazy">
                    @endif
                    <span class="tiktok-feed-play" aria-hidden="true"><i class="fa-solid fa-play"></i></span>
                </div>
                <div class="tiktok-feed-body">
                    @if ($video['author'])
                        <span class="tiktok-feed-author"><i class="fa-brands fa-tiktok"></i> {{ '@'.ltrim($video['author'], '@') }}</span>
                    @endif
                    <p>{{ $video['title'] }}</p>
                </div>
            </button>

            <div class="modal fade tiktok-video-modal" id="tt-video-{{ $loop->index }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content">
                        <button type="button" class="btn-close tiktok-video-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="row g-0">
                            <div class="col-lg-7 tiktok-video-media">
                                {{-- scrolling="no" hides the player's inner scrollbar; the caption below the
                                     video is cut off, and "View on TikTok" links to the full post. --}}
                                <iframe class="tiktok-video-frame" data-src="https://www.tiktok.com/embed/v2/{{ $video['id'] }}"
                                        title="TikTok video" scrolling="no" allow="autoplay; encrypted-media; fullscreen"></iframe>
                            </div>
                            <div class="col-lg-5 tiktok-video-text">
                                <div class="tiktok-video-head">
                                    <i class="fa-brands fa-tiktok tiktok-video-icon"></i>
                                    <strong>{{ $video['author'] ? '@'.ltrim($video['author'], '@') : 'TikTok' }}</strong>
                                </div>
                                <p class="tiktok-video-title">{{ $video['title'] }}</p>
                                <a class="btn_one tiktok-video-link" href="{{ $video['url'] }}" target="_blank" rel="noopener">View on TikTok <i class="ti-arrow-top-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @if ($pageUrl)
        <div class="text-center mt-4">
            <a class="btn_one" href="{{ $pageUrl }}" target="_blank" rel="noopener">Follow us on TikTok <i class="ti-arrow-top-right"></i></a>
        </div>
    @endif
</div>

