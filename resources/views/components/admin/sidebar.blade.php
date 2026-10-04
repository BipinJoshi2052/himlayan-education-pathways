@php
    $navItems = app(\App\Services\AdminNavigationService::class)->visibleTo(auth()->user());
@endphp

<div class="sidebar-header d-flex align-items-center justify-content-start">
    <a href="{{ route('admin.dashboard') }}" class="navbar-brand">
        <h4 class="logo-title">{{ config('app.name', 'Laravel') }}</h4>
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
    </div>
</div>
