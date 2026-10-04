@extends('layouts.admin')

@section('title', 'Services & Courses')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Services & Courses</h4>
        <a href="{{ route('admin.services.create') }}" class="btn btn-primary">+ New Service</a>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if (session('status_warning'))
        <div class="alert alert-warning">{{ session('status_warning') }}</div>
    @endif

    <x-admin.trash-tabs route="admin.services" :active-count="$activeCount" :trashed-count="$trashedCount" />

    <form method="GET" class="row g-2 mb-3">
        @if (request('trash'))
            <input type="hidden" name="trash" value="{{ request('trash') }}">
        @endif
        <div class="col-auto">
            <input type="text" name="filter[search]" class="form-control" placeholder="Search title or slug..."
                   value="{{ request('filter.search') }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-outline-primary">Filter</button>
        </div>
        @if (request('filter.search'))
            <div class="col-auto">
                <a href="{{ route('admin.services.index', array_filter(['trash' => request('trash')])) }}" class="btn btn-link">Clear</a>
            </div>
        @endif
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th style="width: 40px;"></th>
                        <th>Title</th>
                        <th>Duration</th>
                        <th>Fee</th>
                        <th>Featured</th>
                        <th>Active</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody data-sortable-url="{{ route('admin.reorder', 'Service') }}">
                    @forelse ($services as $service)
                        <tr data-id="{{ $service->id }}">
                            <td>
                                @unless ($service->trashed())
                                    <span class="drag-handle" style="cursor: grab;">
                                        <svg class="icon-18" width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="9" cy="6" r="1.5" fill="currentColor"/><circle cx="15" cy="6" r="1.5" fill="currentColor"/>
                                            <circle cx="9" cy="12" r="1.5" fill="currentColor"/><circle cx="15" cy="12" r="1.5" fill="currentColor"/>
                                            <circle cx="9" cy="18" r="1.5" fill="currentColor"/><circle cx="15" cy="18" r="1.5" fill="currentColor"/>
                                        </svg>
                                    </span>
                                @endunless
                            </td>
                            <td>{{ $service->title }}</td>
                            <td>{{ $service->duration ?: '—' }}</td>
                            <td>{{ $service->fee !== null ? number_format((float) $service->fee, 2) : '—' }}</td>
                            <td>
                                @if ($service->is_featured)
                                    <span class="badge bg-warning-subtle text-warning">Featured</span>
                                @endif
                            </td>
                            <td>
                                @if ($service->is_active)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @include('admin.partials.row-actions', ['routeBase' => 'admin.services', 'model' => $service])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No services found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $services->links() }}
    </div>
@endsection
