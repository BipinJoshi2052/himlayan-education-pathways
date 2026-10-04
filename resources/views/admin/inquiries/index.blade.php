@extends('layouts.admin')

@section('title', 'Inquiries')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">
            Inquiries
            @if ($unreadCount > 0)
                <span class="badge bg-primary-subtle text-primary ms-2">{{ $unreadCount }} unread</span>
            @endif
        </h4>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if (session('status_warning'))
        <div class="alert alert-warning">{{ session('status_warning') }}</div>
    @endif

    <x-admin.trash-tabs route="admin.inquiries" :active-count="$activeCount" :trashed-count="$trashedCount" />

    <form method="GET" class="row g-2 mb-3">
        @if (request('trash'))
            <input type="hidden" name="trash" value="{{ request('trash') }}">
        @endif
        <div class="col-auto">
            <input type="text" name="filter[search]" class="form-control" placeholder="Search name, email, subject..."
                   value="{{ request('filter.search') }}">
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
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>From</th>
                        <th>Message</th>
                        <th>Location</th>
                        <th>Received</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inquiries as $inquiry)
                        <tr class="{{ $inquiry->status->value === 'unread' ? 'bg-light' : '' }}">
                            <td>
                                <a href="{{ route('admin.inquiries.show', $inquiry) }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($inquiry->status->value === 'unread')
                                            <strong>{{ $inquiry->name }}</strong>
                                        @else
                                            <span>{{ $inquiry->name }}</span>
                                        @endif
                                        <span class="badge {{ $inquiry->status->badgeClass() }}">{{ $inquiry->status->label() }}</span>
                                    </div>
                                    <div class="text-muted small">{{ $inquiry->email }}</div>
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('admin.inquiries.show', $inquiry) }}" class="text-decoration-none text-body">
                                    <div class="small">{{ $inquiry->subject ?: '(no subject)' }}</div>
                                    <div class="text-muted small">{{ \Illuminate\Support\Str::limit($inquiry->message, 80) }}</div>
                                </a>
                            </td>
                            <td class="small">
                                @if ($inquiry->location && ($inquiry->location['city'] ?? $inquiry->location['country'] ?? null))
                                    {{ implode(', ', array_filter([$inquiry->location['city'] ?? null, $inquiry->location['region'] ?? null, $inquiry->location['country'] ?? null])) }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                                <div class="text-muted">{{ $inquiry->ip_address }}</div>
                            </td>
                            <td class="text-muted small">{{ $inquiry->created_at->diffForHumans() }}</td>
                            <td class="text-end">
                                @include('admin.partials.row-actions', ['routeBase' => 'admin.inquiries', 'model' => $inquiry, 'showEdit' => false])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No inquiries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $inquiries->links() }}
    </div>
@endsection
