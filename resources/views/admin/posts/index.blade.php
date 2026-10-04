@extends('layouts.admin')

@section('title', 'Posts & News')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Posts & News</h4>
        <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">+ New Post</a>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if (session('status_warning'))
        <div class="alert alert-warning">{{ session('status_warning') }}</div>
    @endif

    <x-admin.trash-tabs route="admin.posts" :active-count="$activeCount" :trashed-count="$trashedCount" />

    <form method="GET" class="row g-2 mb-3">
        @if (request('trash'))
            <input type="hidden" name="trash" value="{{ request('trash') }}">
        @endif
        <div class="col-auto">
            <input type="text" name="filter[search]" class="form-control" placeholder="Search title or slug..."
                   value="{{ request('filter.search') }}">
        </div>
        <div class="col-auto">
            <select name="filter[type]" class="form-select">
                <option value="">All types</option>
                @foreach ($types as $type)
                    <option value="{{ $type->value }}" @selected(request('filter.type') === $type->value)>{{ $type->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <select name="filter[status]" class="form-select">
                <option value="">All statuses</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected(request('filter.status') === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-outline-primary">Filter</button>
        </div>
        @if (request('filter.search') || request('filter.type') || request('filter.status'))
            <div class="col-auto">
                <a href="{{ route('admin.posts.index', array_filter(['trash' => request('trash')])) }}" class="btn btn-link">Clear</a>
            </div>
        @endif
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th style="width: 40px;"></th>
                        <th></th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Featured</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody data-sortable-url="{{ route('admin.reorder', 'Post') }}">
                    @forelse ($posts as $post)
                        <tr data-id="{{ $post->id }}">
                            <td>
                                @unless ($post->trashed())
                                    <span class="drag-handle" style="cursor: grab;">
                                        <svg class="icon-18" width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="9" cy="6" r="1.5" fill="currentColor"/><circle cx="15" cy="6" r="1.5" fill="currentColor"/>
                                            <circle cx="9" cy="12" r="1.5" fill="currentColor"/><circle cx="15" cy="12" r="1.5" fill="currentColor"/>
                                            <circle cx="9" cy="18" r="1.5" fill="currentColor"/><circle cx="15" cy="18" r="1.5" fill="currentColor"/>
                                        </svg>
                                    </span>
                                @endunless
                            </td>
                            <td>
                                @if ($post->getFirstMediaUrl('featured_image', 'thumb'))
                                    <img src="{{ $post->getFirstMediaUrl('featured_image', 'thumb') }}" alt="" class="rounded" style="width: 48px; height: 32px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary-subtle rounded" style="width: 48px; height: 32px;"></div>
                                @endif
                            </td>
                            <td>{{ $post->title }}</td>
                            <td><span class="badge bg-primary-subtle text-primary">{{ $post->type->label() }}</span></td>
                            <td>
                                @if ($post->is_featured)
                                    <span class="badge bg-warning-subtle text-warning">Featured</span>
                                @endif
                            </td>
                            <td><span class="badge {{ $post->status->badgeClass() }}">{{ $post->status->label() }}</span></td>
                            <td class="text-end">
                                @include('admin.partials.row-actions', ['routeBase' => 'admin.posts', 'model' => $post])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No posts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $posts->links() }}
    </div>
@endsection
