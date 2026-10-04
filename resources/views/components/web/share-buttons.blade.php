@props(['url', 'title'])

@php
    $encodedUrl = urlencode($url);
    $encodedTitle = urlencode($title);
@endphp

<div class="share-buttons">
    <span class="share-buttons-label">Share:</span>
    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedUrl }}" target="_blank" rel="noopener"
       class="share-btn share-btn-facebook" aria-label="Share on Facebook">
        <i class="fa-brands fa-facebook-f"></i>
    </a>
    <a href="https://twitter.com/intent/tweet?url={{ $encodedUrl }}&text={{ $encodedTitle }}" target="_blank" rel="noopener"
       class="share-btn share-btn-twitter" aria-label="Share on X / Twitter">
        <i class="fa-brands fa-x-twitter"></i>
    </a>
    <a href="https://wa.me/?text={{ $encodedTitle }}%20{{ $encodedUrl }}" target="_blank" rel="noopener"
       class="share-btn share-btn-whatsapp" aria-label="Share on WhatsApp">
        <i class="fa-brands fa-whatsapp"></i>
    </a>
    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $encodedUrl }}" target="_blank" rel="noopener"
       class="share-btn share-btn-linkedin" aria-label="Share on LinkedIn">
        <i class="fa-brands fa-linkedin-in"></i>
    </a>
    <button type="button" class="share-btn share-btn-copy" data-share-url="{{ $url }}" aria-label="Copy link">
        <i class="ti-link"></i>
    </button>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.share-btn-copy').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        var url = btn.dataset.shareUrl;
                        var done = function () {
                            var icon = btn.querySelector('i');
                            var original = icon.className;
                            icon.className = 'ti-check';
                            setTimeout(function () { icon.className = original; }, 1500);
                        };

                        if (navigator.clipboard) {
                            navigator.clipboard.writeText(url).then(done).catch(function () {
                                window.prompt('Copy this link:', url);
                            });
                        } else {
                            window.prompt('Copy this link:', url);
                        }
                    });
                });
            });
        </script>
    @endpush
@endonce
