@props(['entity' => null])

@php
    $seo = \App\Common\Services\SeoService::resolve($entity);
@endphp

<title>{{ $seo['title'] }}</title>

@if ($seo['description'])
    <meta name="description" content="{{ $seo['description'] }}">
@endif

@if ($seo['keywords'])
    <meta name="keywords" content="{{ $seo['keywords'] }}">
@endif

<link rel="canonical" href="{{ url()->current() }}">

<meta property="og:title" content="{{ $seo['title'] }}">
@if ($seo['description'])
    <meta property="og:description" content="{{ $seo['description'] }}">
@endif
@if ($seo['og_image'])
    <meta property="og:image" content="{{ $seo['og_image'] }}">
@endif
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:type" content="website">

<meta name="twitter:card" content="{{ \App\Models\Setting::get('twitter_card_type', 'summary') }}">
<meta name="twitter:title" content="{{ $seo['title'] }}">
@if ($seo['description'])
    <meta name="twitter:description" content="{{ $seo['description'] }}">
@endif
@if ($seo['og_image'])
    <meta name="twitter:image" content="{{ $seo['og_image'] }}">
@endif
@if ($seo['twitter_handle'])
    <meta name="twitter:site" content="{{ '@'.ltrim($seo['twitter_handle'], '@') }}">
@endif

@if (\App\Models\Setting::get('google_site_verification'))
    <meta name="google-site-verification" content="{{ \App\Models\Setting::get('google_site_verification') }}">
@endif
