@extends('layouts.admin')

@section('title', 'Content Sections')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Content Sections</h4>
        <a href="{{ route('admin.sections.create') }}" class="btn btn-primary">+ New Section</a>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if (session('status_warning'))
        <div class="alert alert-warning">{{ session('status_warning') }}</div>
    @endif

    <x-admin.trash-tabs route="admin.sections" :active-count="$activeCount" :trashed-count="$trashedCount" />

    <form method="GET" class="row g-2 mb-3">
        @if (request('trash'))
            <input type="hidden" name="trash" value="{{ request('trash') }}">
        @endif
        <div class="col-auto">
            <input type="text" name="filter[search]" class="form-control" placeholder="Search page, key, title..."
                   value="{{ request('filter.search') }}">
        </div>
        <div class="col-auto">
            <input type="text" name="filter[page_slug]" class="form-control" placeholder="Filter by page slug"
                   value="{{ request('filter.page_slug') }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-outline-primary">Filter</button>
        </div>
        @if (request('filter.page_slug') || request('filter.search'))
            <div class="col-auto">
                <a href="{{ route('admin.sections.index', array_filter(['trash' => request('trash')])) }}" class="btn btn-link">Clear</a>
            </div>
        @endif
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px;">Image</th>
                        <th>Page</th>
                        <th>Key</th>
                        <th>Layout</th>
                        <th>Items</th>
                        <th>Active</th>
                        <th>Sort</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sections as $section)
                        <tr>
                            <td>
                                @if ($section->getFirstMediaUrl('image'))
                                    <img src="{{ $section->getFirstMediaUrl('image') }}" alt="" style="width: 44px; height: 44px; object-fit: cover; border-radius: 4px;">
                                @endif
                            </td>
                            <td>{{ $section->page_slug }}</td>
                            <td><code>{{ $section->key }}</code></td>
                            <td>{{ $section->layout_type->label() }}</td>
                            <td>{{ $section->items_count }}</td>
                            <td>
                                @if ($section->is_active)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $section->sort_order }}</td>
                            <td class="text-end">
                                @include('admin.partials.row-actions', ['routeBase' => 'admin.sections', 'model' => $section])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No sections found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $sections->links() }}
    </div>
@endsection
