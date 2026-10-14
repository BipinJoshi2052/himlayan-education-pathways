{{--
    One site-wide popup notice. Text, an image, or both — any combination,
    with an optional link button. Several of these can be on the page at
    once (one per active Notice, in sort_order — see layouts/web.blade.php);
    the shared script below queues them and shows one at a time, in order,
    moving to the next as soon as the current one is closed. Dismissal is
    remembered per-browser via localStorage, keyed by the notice's id +
    updated_at so editing the notice (even just re-saving it) makes it show
    again for everyone, including visitors who already dismissed the old
    version.
--}}
<div class="modal fade notice-popup-modal" id="notice-popup-{{ $notice->id }}" tabindex="-1" aria-hidden="true" data-notice-key="notice-dismissed-{{ $notice->id }}-{{ $notice->updated_at?->timestamp }}" data-display-mode="{{ $notice->display_mode }}">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" style="z-index: 1;" data-bs-dismiss="modal" aria-label="Close"></button>

            @if ($notice->getFirstMediaUrl('image', 'web'))
                <img src="{{ $notice->getFirstMediaUrl('image', 'web') }}" alt="{{ $notice->title ?: 'Notice' }}" class="w-100" style="height: auto; max-height: 80vh; object-fit: contain; border-radius: 0.375rem 0.375rem 0 0;">
            @endif

            @if ($notice->title || $notice->message || $notice->link_url)
                <div class="modal-body text-center p-4">
                    @if ($notice->title)
                        <h2 class="h4 mb-2">{{ $notice->title }}</h2>
                    @endif
                    @if ($notice->message)
                        <p class="mb-3">{!! nl2br(e($notice->message)) !!}</p>
                    @endif
                    @if ($notice->link_url)
                        <a href="{{ $notice->link_url }}" class="btn_one" target="_blank" rel="noopener">
                            {{ $notice->link_text ?: 'Learn more' }}
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

@once('notice-popup-script')
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (!window.bootstrap) return;

                // Built once for every .notice-popup-modal on the page (there's one
                // per active notice), in the DOM order they were rendered in —
                // which is already sort_order, from the query that builds them.
                var queue = Array.prototype.slice.call(document.querySelectorAll('.notice-popup-modal'));

                function showNext() {
                    var el = queue.shift();
                    if (!el) return;

                    var key = el.dataset.noticeKey;
                    var mode = el.dataset.displayMode || 'once_forever';
                    var storage = mode === 'once_per_session' ? window.sessionStorage : (mode === 'once_forever' ? window.localStorage : null);

                    var alreadySeen = false;
                    try {
                        alreadySeen = storage ? storage.getItem(key) === '1' : false;
                    } catch (e) {
                        // Storage blocked (private mode, etc.) — show it rather than hide it.
                    }

                    if (alreadySeen) {
                        showNext();
                        return;
                    }

                    var modal = new bootstrap.Modal(el);
                    modal.show();

                    el.addEventListener('hidden.bs.modal', function () {
                        if (storage) {
                            try {
                                storage.setItem(key, '1');
                            } catch (e) {
                                // Nothing to do if storage isn't available.
                            }
                        }
                        showNext();
                    });
                }

                showNext();
            });
        </script>
    @endpush
@endonce
