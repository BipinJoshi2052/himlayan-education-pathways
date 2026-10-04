@extends('layouts.admin')

@section('title', 'Galleries')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Galleries</h4>
        <a href="{{ route('admin.galleries.create') }}" class="btn btn-primary">+ New Gallery</a>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if (session('status_warning'))
        <div class="alert alert-warning">{{ session('status_warning') }}</div>
    @endif

    <x-admin.trash-tabs route="admin.galleries" :active-count="$activeCount" :trashed-count="$trashedCount" />

    <form method="GET" class="row g-2 mb-3">
        @if (request('trash'))
            <input type="hidden" name="trash" value="{{ request('trash') }}">
        @endif
        <div class="col-auto">
            <input type="text" name="filter[search]" class="form-control" placeholder="Search title..."
                   value="{{ request('filter.search') }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-outline-primary">Filter</button>
        </div>
    </form>

    <div class="row g-4">
        @forelse ($galleries as $gallery)
            <div class="col-md-4 col-xl-3">
                <div class="card h-100">
                    <div style="height: 160px; background: #eee;" class="d-flex align-items-center justify-content-center overflow-hidden">
                        @if ($gallery->getFirstMediaUrl('cover'))
                            <img src="{{ $gallery->getFirstMediaUrl('cover') }}" class="w-100 h-100" style="object-fit: cover;" alt="">
                        @else
                            <span class="text-muted small">No cover</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="mb-0">{{ $gallery->title }}</h6>
                            @if ($gallery->is_featured)
                                <span class="badge bg-warning-subtle text-warning">Featured</span>
                            @endif
                        </div>
                        <p class="text-muted small mb-2">{{ $gallery->photos_count }} photo(s)</p>
                        @if ($gallery->is_active)
                            <span class="badge bg-success-subtle text-success mb-3">Active</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary mb-3">Inactive</span>
                        @endif
                        <div>
                            @include('admin.partials.row-actions', ['routeBase' => 'admin.galleries', 'model' => $gallery])
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-center text-muted py-4">No galleries found.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $galleries->links() }}
    </div>
@endsection
