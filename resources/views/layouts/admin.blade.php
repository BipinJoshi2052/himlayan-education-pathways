<!DOCTYPE html>
@php
    // User.primary_color/secondary_color are NOT NULL columns with their own
    // DB defaults (#2563eb / #64748b from the Phase 1 migration), and there's
    // no "My Profile" page yet letting a user actually change their own —
    // so `auth()->user()->primary_color ?? ...` would NEVER fall through to
    // the global Setting; it's always sitting at that unreached column
    // default. The global Settings > Appearance value takes priority here
    // instead, which is what actually produces a visible effect right now.
    // Once a per-user profile page exists, invert this back to
    // user-override-first — see docs/settings.md.
    $adminPrimaryColor = \App\Models\Setting::get('primary_color') ?? auth()->user()?->primary_color ?? '#3a57e8';
    $adminSecondaryColor = \App\Models\Setting::get('secondary_color') ?? auth()->user()?->secondary_color ?? '#64748b';
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      data-bs-theme="{{ auth()->user()->theme_mode ?? 'light' }}"
      style="--bs-primary: {{ $adminPrimaryColor }}; --bs-secondary: {{ $adminSecondaryColor }};">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Laravel') }}</title>

        @if ($siteLogo = \App\Models\Setting::get('site_logo'))
            <link rel="icon" href="{{ $siteLogo }}">
            <link rel="apple-touch-icon" href="{{ $siteLogo }}">
        @endif

        <link rel="stylesheet" href="{{ asset('admin-assets/css/hope-ui.min.css') }}">
        <link rel="stylesheet" href="{{ asset('admin-assets/css/custom.min.css') }}">
        <link rel="stylesheet" href="{{ asset('admin-assets/css/admin.css') }}">

        {{-- Quill (rich-text), CDN-only, no build step — see docs/shared-crud-blocks.md --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons@7/css/flag-icons.min.css">
    </head>
    <body>
        <aside class="sidebar sidebar-default sidebar-white sidebar-base navs-rounded-all">
            <x-admin.sidebar />
        </aside>

        <main class="main-content">
            <x-admin.navbar />

            <div class="container-fluid content-inner py-4">
                @yield('content')
            </div>
        </main>

        <script src="{{ asset('admin-assets/js/libs.min.js') }}"></script>
        <script src="{{ asset('admin-assets/js/hope-ui.js') }}"></script>
        <script src="{{ asset('admin-assets/js/admin.js') }}"></script>

        {{-- CDN-only, no npm/bundling — see docs/shared-crud-blocks.md --}}
        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

        <script src="{{ asset('admin-assets/js/locale-tabs.js') }}"></script>
        <script src="{{ asset('admin-assets/js/rich-text.js') }}"></script>
        <script src="{{ asset('admin-assets/js/sortable-reorder.js') }}"></script>
        <script src="{{ asset('admin-assets/js/delete-confirm.js') }}"></script>
        @yield('scripts')
    </body>
</html>
