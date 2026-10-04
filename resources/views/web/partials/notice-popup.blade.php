{{--
    Site-wide popup notice. Text, an image, or both — any combination, with
    an optional link button. Dismissal is remembered per-browser via
    localStorage, keyed by the notice's id + updated_at so editing the
    notice (even just re-saving it) makes it show again for everyone,
    including visitors who already dismissed the old version.
--}}
<div class="modal fade" id="notice-popup" tabindex="-1" aria-hidden="true" data-notice-key="notice-dismissed-{{ $notice->id }}-{{ $notice->updated_at?->timestamp }}" data-display-mode="{{ $notice->display_mode }}">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" style="z-index: 1;" data-bs-dismiss="modal" aria-label="Close"></button>

            @if ($notice->getFirstMediaUrl('image'))
                <img src="{{ $notice->getFirstMediaUrl('image') }}" alt="{{ $notice->title ?: 'Notice' }}" class="w-100" style="max-height: 320px; object-fit: cover; border-radius: 0.375rem 0.375rem 0 0;">
            @endif

            @if ($notice->title || $notice->message || $notice->link_url)
                <div class="modal-body text-center p-4">
                    @if ($notice->title)
                        <h4 class="mb-2">{{ $notice->title }}</h4>
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.getElementById('notice-popup');
            if (!el || !window.bootstrap) return;

            var key = el.dataset.noticeKey;
            var mode = el.dataset.displayMode || 'once_forever';

            var storage = mode === 'once_per_session' ? window.sessionStorage : (mode === 'once_forever' ? window.localStorage : null);
            var alreadySeen = false;
            try {
                alreadySeen = storage ? storage.getItem(key) === '1' : false;
            } catch (e) {
                // Storage blocked (private mode, etc.) — show it rather than hide it.
            }

            if (alreadySeen) return;

            var modal = new bootstrap.Modal(el);
            modal.show();

            if (!storage) return;
            el.addEventListener('hidden.bs.modal', function () {
                try {
                    storage.setItem(key, '1');
                } catch (e) {
                    // Nothing to do if storage isn't available.
                }
            });
        });
    </script>
@endpush
