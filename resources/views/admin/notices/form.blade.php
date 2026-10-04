@extends('layouts.admin')

@section('title', $notice->exists ? 'Edit Notice' : 'New Notice')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">{{ $notice->exists ? 'Edit Notice' : 'New Notice' }}</h4>
        <a href="{{ route('admin.notices.index') }}" class="btn btn-link">&larr; Back to notices</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ $notice->exists ? route('admin.notices.update', $notice) : route('admin.notices.store') }}"
          enctype="multipart/form-data">
        @csrf
        @if ($notice->exists)
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-lg-8">
                @include('admin.partials.locale-tabs', ['locales' => $locales])

                <div class="card mb-4">
                    <div class="card-body">
                        <p class="text-muted small">A notice needs a message, an image, or both — title and link are always optional.</p>

                        <div class="form-group">
                            <label class="form-label">Title (optional)</label>
                            @foreach ($locales as $i => $locale)
                                <div data-locale-pane="{{ $locale }}" style="{{ $i === 0 ? '' : 'display:none' }}">
                                    <input type="text" class="form-control mb-2" name="title[{{ $locale }}]"
                                           value="{{ old('title.'.$locale, $notice->getTranslation('title', $locale, false)) }}">
                                </div>
                            @endforeach
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label">Message</label>
                            @foreach ($locales as $i => $locale)
                                <div data-locale-pane="{{ $locale }}" style="{{ $i === 0 ? '' : 'display:none' }}">
                                    <textarea class="form-control mb-2" rows="4" name="message[{{ $locale }}]">{{ old('message.'.$locale, $notice->getTranslation('message', $locale, false)) }}</textarea>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">Image</h5></div>
                    <div class="card-body">
                        @if ($notice->exists && $notice->getFirstMediaUrl('image'))
                            <img src="{{ $notice->getFirstMediaUrl('image') }}" class="img-fluid rounded mb-2" alt="Current image" style="max-height: 200px;">
                        @endif
                        <input type="file" class="form-control" name="image" accept="image/*">
                        <small class="text-muted">Optional — shown above the message in the popup. Leave blank to keep the current image.</small>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">Link (optional)</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="form-label">URL</label>
                                    <input type="text" class="form-control" name="link_url" placeholder="https://..."
                                           value="{{ old('link_url', $notice->link_url) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label class="form-label">Link text</label>
                                    <input type="text" class="form-control" name="link_text" placeholder="Learn more"
                                           value="{{ old('link_text', $notice->link_text) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">Display</h5></div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                   @checked(old('is_active', $notice->exists ? $notice->is_active : true))>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Starts at (optional)</label>
                            <input type="datetime-local" class="form-control" name="starts_at"
                                   value="{{ old('starts_at', $notice->starts_at?->format('Y-m-d\TH:i')) }}">
                            <small class="text-muted">Leave blank to show immediately.</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Ends at (optional)</label>
                            <input type="datetime-local" class="form-control" name="ends_at"
                                   value="{{ old('ends_at', $notice->ends_at?->format('Y-m-d\TH:i')) }}">
                            <small class="text-muted">Leave blank to show indefinitely.</small>
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label">Sort order</label>
                            <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', $notice->sort_order ?? 0) }}">
                            <small class="text-muted">When more than one notice is active, the lowest sort order shows first.</small>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">{{ $notice->exists ? 'Update Notice' : 'Create Notice' }}</button>
            </div>
        </div>
    </form>
@endsection
