@php
    $navItems = app(\App\Services\AdminNavigationService::class)->visibleTo(auth()->user());
@endphp

<div class="sidebar-header d-flex align-items-center justify-content-start">
    <a href="{{ route('admin.dashboard') }}" class="navbar-brand">
        @if (\App\Models\Setting::get('site_logo'))
            <img src="{{ \App\Models\Setting::get('site_logo') }}" alt="{{ config('app.name', 'Laravel') }}" class="admin-brand-logo" style="max-height: 64px; max-width: 100%; width: auto;">
        @else
            <h4 class="logo-title" title="{{ config('app.name', 'Laravel') }}">{{ config('app.name', 'Laravel') }}</h4>
        @endif
    </a>
    <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
        <i class="icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.25 12.2744L19.25 12.2744" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                <path d="M10.2998 18.2988L4.2498 12.2748L10.2998 6.24976" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </i>
    </div>
</div>

<div class="sidebar-body data-scrollbar">
    <div class="sidebar-account-mobile">
        <button type="button" class="theme-toggle-btn btn btn-sm btn-soft-primary rounded-pill w-100 mb-2">
            <span class="theme-toggle-label">{{ ucfirst(auth()->user()->theme_mode ?? 'light') }} mode</span>
        </button>
        <div class="px-2 mb-2 fw-semibold">{{ auth()->user()->name ?? 'Guest' }}</div>
    </div>
    <div class="sidebar-list">
        <ul class="navbar-nav iq-main-menu" id="sidebar-menu">
            @foreach ($navItems as $item)
                <li class="nav-item">
                    <a class="nav-link {{ $item->isActive() ? 'active' : '' }}"
                       @if ($item->isActive()) aria-current="page" @endif
                       href="{{ route($item->route) }}">
                        <i class="icon">{!! $item->icon !!}</i>
                        <span class="item-name">{{ $item->label }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="sidebar-account-mobile sidebar-account-bottom">
            <form method="POST" action="{{ route('logout') }}" class="m-0 px-2">
                @csrf
                <button type="submit" class="btn btn-danger w-100">Log out</button>
            </form>
        </div>
    </div>
</div>
