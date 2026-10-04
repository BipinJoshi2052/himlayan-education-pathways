@props(['route', 'activeCount', 'trashedCount'])

<ul class="nav nav-pills mb-3">
    <li class="nav-item">
        <a class="nav-link {{ request('trash') !== 'only' ? 'active' : '' }}" href="{{ route($route.'.index') }}">
            All
            <span class="badge bg-primary-subtle text-primary ms-1">{{ $activeCount }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('trash') === 'only' ? 'active' : '' }}" href="{{ route($route.'.index', ['trash' => 'only']) }}">
            Trash
            <span class="badge bg-danger-subtle text-danger ms-1">{{ $trashedCount }}</span>
        </a>
    </li>
</ul>
