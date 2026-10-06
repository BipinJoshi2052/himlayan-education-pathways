@props(['data'])

{{-- One JSON-LD block per schema. Encoded with unicode and slash escaping so
     Nepali text and URLs stay valid inside the script tag. --}}
<script type="application/ld+json">{!! json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
