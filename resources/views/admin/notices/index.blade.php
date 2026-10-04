@extends('layouts.admin')

@section('title', 'Notices')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Notices</h4>
        <a href="{{ route('admin.notices.create') }}" class="btn btn-primary">+ New Notice</a>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if (session('status_warning'))
        <div class="alert alert-warning">{{ session('status_warning') }}</div>
    @endif

    <x-admin.trash-tabs route="admin.notices" :active-count="$activeCount" :trashed-count="$trashedCount" />

    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px;"></th>
                        <th>Title / Message</th>
                        <th>Window</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($notices as $notice)
                        <tr>
                            <td>
                                @if ($notice->getFirstMediaUrl('image'))
                                    <img src="{{ $notice->getFirstMediaUrl('image') }}" alt="" style="width: 48px; height: 48px; object-fit: cover; border-radius: 4px;">
                                @endif
                            </td>
                            <td>
                                <div>{{ $notice->title ?: '(no title)' }}</div>
                                <div class="text-muted small">{{ \Illuminate\Support\Str::limit($notice->message, 80) }}</div>
                            </td>
                            <td class="small text-muted">
                                {{ $notice->starts_at?->format('Y-m-d') ?? 'anytime' }}
                                &rarr;
                                {{ $notice->ends_at?->format('Y-m-d') ?? 'no end' }}
                            </td>
                            <td>
                                @if ($notice->is_active)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @include('admin.partials.row-actions', ['routeBase' => 'admin.notices', 'model' => $notice])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No notices found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $notices->links() }}
    </div>
@endsection
