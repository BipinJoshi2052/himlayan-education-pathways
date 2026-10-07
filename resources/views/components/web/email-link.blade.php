@props(['email', 'class' => null])

{{-- Hides the address from simple scrapers that only read the static HTML:
     the visible text and the real mailto: link are both built by JavaScript
     from two separate data attributes, only once they're joined together. --}}
@php
    [$user, $domain] = array_pad(explode('@', (string) $email, 2), 2, '');
@endphp
<a href="#" class="obfuscated-email {{ $class }}" data-user="{{ $user }}" data-domain="{{ $domain }}" rel="nofollow">{{ $user }} [at] {{ str_replace('.', ' [dot] ', $domain) }}</a>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.obfuscated-email').forEach(function (el) {
                    var user = el.dataset.user, domain = el.dataset.domain;
                    if (!user || !domain) {
                        return;
                    }
                    var email = user + '@' + domain;
                    el.setAttribute('href', 'mailto:' + email);
                    el.textContent = email;
                });
            });
        </script>
    @endpush
@endonce
