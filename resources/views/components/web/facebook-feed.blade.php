@props(['posts', 'pageUrl' => null, 'pageName' => null, 'pageLogo' => null])

{{-- Latest posts from the institute's Facebook Page, drawn as site cards. A card
     opens a popup with the video or photo on the left and the post text on the
     right. Video playback uses Facebook's video plugin, parsed when the popup opens. --}}
<div class="facebook-feed">
    <div class="facebook-feed-grid">
        @foreach ($posts as $post)
            <button type="button" class="facebook-feed-card" data-bs-toggle="modal" data-bs-target="#fb-post-{{ $loop->index }}">
                <div class="facebook-feed-image">
                    @if ($post['image'])
                        <img src="{{ $post['image'] }}" alt="" loading="lazy">
                    @endif
                    @if ($post['video'] ?? false)
                        <span class="facebook-feed-play" aria-hidden="true"><i class="fa-solid fa-play"></i></span>
                    @endif
                </div>
                <div class="facebook-feed-body">
                    <span class="facebook-feed-date"><i class="fa-brands fa-facebook"></i> {{ $post['date'] }}</span>
                    @if ($post['message'])
                        <p>{{ $post['message'] }}</p>
                    @endif
                </div>
            </button>

            <div class="modal fade facebook-post-modal" id="fb-post-{{ $loop->index }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content">
                        <button type="button" class="btn-close facebook-post-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="row g-0">
                            <div class="col-lg-7 facebook-post-media">
                                @if ($post['video'] ?? false)
                                    <div class="fb-video" data-href="{{ $post['url'] }}" data-width="auto" data-show-text="false"></div>
                                @elseif ($post['image'])
                                    <img src="{{ $post['image'] }}" alt="" class="facebook-post-photo">
                                @endif
                            </div>
                            <div class="col-lg-5 facebook-post-text">
                                <div class="facebook-post-head">
                                    @if ($pageLogo ?? null)
                                        <img src="{{ $pageLogo }}" alt="" class="facebook-post-logo">
                                    @else
                                        <i class="fa-brands fa-facebook facebook-post-icon"></i>
                                    @endif
                                    <div>
                                        <strong>{{ $pageName ?? 'Himalayan Education Pathways' }}</strong>
                                        <span>{{ $post['date'] }}</span>
                                    </div>
                                </div>
                                @if ($post['message'])
                                    <p class="facebook-post-message">{{ $post['message'] }}</p>
                                @endif
                                @if ($post['url'])
                                    <a class="btn_one facebook-post-link" href="{{ $post['url'] }}" target="_blank" rel="noopener">View on Facebook <i class="ti-arrow-top-right"></i></a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @if ($pageUrl)
        <div class="text-center mt-4">
            <a class="btn_one" href="{{ $pageUrl }}" target="_blank" rel="noopener">Follow us on Facebook <i class="ti-arrow-top-right"></i></a>
        </div>
    @endif
</div>

@once
    @push('scripts')
        <script>
            // Each popup's media is reset on close, so a video stops playing and
            // is parsed again the next time its popup opens.
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.facebook-post-modal').forEach(function (modal) {
                    var media = modal.querySelector('.facebook-post-media');
                    if (!media) {
                        return;
                    }
                    var original = media.innerHTML;

                    modal.addEventListener('shown.bs.modal', function () {
                        var video = media.querySelector('.fb-video');
                        if (!video || !window.FB) {
                            return;
                        }

                        // Size the player so the whole video fits: the video's shape comes
                        // from its card thumbnail, and the player's height follows its width.
                        var thumb = document.querySelector('[data-bs-target="#' + modal.id + '"] img');
                        var ratio = thumb && thumb.naturalWidth && thumb.naturalHeight
                            ? thumb.naturalWidth / thumb.naturalHeight
                            : 9 / 16;
                        var maxHeight = window.innerHeight * 0.85;
                        var available = media.clientWidth || 400;
                        var width = Math.floor(Math.min(available, maxHeight * ratio));

                        video.setAttribute('data-width', String(width));
                        FB.XFBML.parse(media);
                    });

                    modal.addEventListener('hidden.bs.modal', function () {
                        media.innerHTML = original;
                    });
                });
            });
        </script>
    @endpush
@endonce
