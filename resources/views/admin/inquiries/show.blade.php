@extends('layouts.admin')

@section('title', 'Inquiry from '.$inquiry->name)

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Inquiry from {{ $inquiry->name }}</h4>
        <a href="{{ route('admin.inquiries.index') }}" class="btn btn-link">&larr; Back to inquiries</a>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Name</dt>
                        <dd class="col-sm-9">{{ $inquiry->name }}</dd>

                        <dt class="col-sm-3">Email</dt>
                        <dd class="col-sm-9"><a href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a></dd>

                        @if ($inquiry->phone)
                            <dt class="col-sm-3">Phone</dt>
                            <dd class="col-sm-9">{{ $inquiry->phone }}</dd>
                        @endif

                        @if ($inquiry->subject)
                            <dt class="col-sm-3">Subject</dt>
                            <dd class="col-sm-9">{{ $inquiry->subject }}</dd>
                        @endif

                        <dt class="col-sm-3">Received</dt>
                        <dd class="col-sm-9">{{ $inquiry->created_at->format('Y-m-d H:i') }} ({{ $inquiry->created_at->diffForHumans() }})</dd>

                        <dt class="col-sm-3">IP Address</dt>
                        <dd class="col-sm-9">{{ $inquiry->ip_address }}</dd>

                        <dt class="col-sm-3">Location</dt>
                        <dd class="col-sm-9">
                            @if ($inquiry->location && ($inquiry->location['city'] ?? $inquiry->location['country'] ?? null))
                                {{ implode(', ', array_filter([$inquiry->location['city'] ?? null, $inquiry->location['region'] ?? null, $inquiry->location['country'] ?? null])) }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">User Agent</dt>
                        <dd class="col-sm-9 text-break small text-muted">{{ $inquiry->user_agent ?: '—' }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Message</h5></div>
                <div class="card-body">
                    <p class="mb-0" style="white-space: pre-wrap;">{{ $inquiry->message }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Status & Notes</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.inquiries.notes', $inquiry) }}">
                        @csrf
                        @method('PATCH')

                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                @foreach (App\Enums\InquiryStatus::cases() as $status)
                                    <option value="{{ $status->value }}" @selected($inquiry->status === $status)>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label">Internal Notes</label>
                            <textarea class="form-control" rows="6" name="notes">{{ old('notes', $inquiry->notes) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-3">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
