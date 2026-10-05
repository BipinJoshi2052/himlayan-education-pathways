@extends('layouts.admin')

@section('title', $gallery->exists ? 'Edit Gallery' : 'New Gallery')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">{{ $gallery->exists ? 'Edit Gallery' : 'New Gallery' }}</h4>
        <a href="{{ route('admin.galleries.index') }}" class="btn btn-link">&larr; Back to galleries</a>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

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
          action="{{ $gallery->exists ? route('admin.galleries.update', $gallery) : route('admin.galleries.store') }}"
          enctype="multipart/form-data">
        @csrf
        @if ($gallery->exists)
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-lg-8">
                @include('admin.partials.locale-tabs', ['locales' => $locales])

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Title</label>
                            @foreach ($locales as $i => $locale)
                                <div data-locale-pane="{{ $locale }}" style="{{ $i === 0 ? '' : 'display:none' }}">
                                    <input type="text" class="form-control mb-2" name="title[{{ $locale }}]"
                                           value="{{ old('title.'.$locale, $gallery->getTranslation('title', $locale, false)) }}"
                                           @if ($locale === 'en') required @endif>
                                </div>
                            @endforeach
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label">Description</label>
                            @foreach ($locales as $i => $locale)
                                <div data-locale-pane="{{ $locale }}" style="{{ $i === 0 ? '' : 'display:none' }}">
                                    <textarea class="form-control mb-2" rows="4" name="description[{{ $locale }}]">{{ old('description.'.$locale, $gallery->getTranslation('description', $locale, false)) }}</textarea>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                @include('components.admin.seo-inputs', ['model' => $gallery, 'locales' => $locales])
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">Settings</h5></div>
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                   @checked(old('is_active', $gallery->exists ? $gallery->is_active : true))>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1"
                                   @checked(old('is_featured', $gallery->is_featured))>
                            <label class="form-check-label" for="is_featured">Featured</label>
                        </div>
                        <div class="form-group mb-0">
                            <label class="form-label">Sort order</label>
                            <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', $gallery->sort_order ?? 0) }}">
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">Cover Image</h5></div>
                    <div class="card-body">
                        @if ($gallery->exists && $gallery->getFirstMediaUrl('cover'))
                            @include('admin.partials.existing-image', [
                                'url' => $gallery->getFirstMediaUrl('cover'),
                                'alt' => 'Current cover',
                                'removeName' => 'remove_cover',
                                'style' => 'max-height: 200px;',
                            ])
                        @endif
                        <input type="file" class="form-control" name="cover" accept="image/*">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">{{ $gallery->exists ? 'Update Gallery' : 'Create Gallery & Add Photos' }}</button>
            </div>
        </div>
    </form>

    @if ($gallery->exists)
        <hr class="hr-horizontal my-4">

        <h5 class="mb-3">Photos</h5>

        <div id="gallery-dropzone" class="dropzone border rounded mb-2" data-upload-url="{{ route('admin.galleries.upload', $gallery) }}"></div>

        <ul id="upload-list" class="list-group mb-4"></ul>

        <div class="row g-3" id="photo-grid" data-reorder-url="{{ route('admin.galleries.media-reorder', $gallery) }}">
            @foreach ($gallery->getMedia('photos') as $photo)
                <div class="col-md-3 photo-grid-item" data-id="{{ $photo->id }}">
                    <div class="card">
                        <img src="{{ $photo->getUrl() }}" class="card-img-top" style="height: 140px; object-fit: cover; cursor: grab;" alt="">
                        <div class="card-body p-2">
                            <input type="text" class="form-control form-control-sm mb-1 photo-caption-input"
                                   data-media-id="{{ $photo->id }}" placeholder="Caption"
                                   value="{{ $photo->getCustomProperty('caption') }}">
                            <input type="text" class="form-control form-control-sm mb-2 photo-alt-input"
                                   data-media-id="{{ $photo->id }}" placeholder="Alt text"
                                   value="{{ $photo->getCustomProperty('alt_text') }}">
                            <button type="button" class="btn btn-sm btn-soft-danger w-100 photo-delete-btn" data-media-id="{{ $photo->id }}">Delete</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

@section('scripts')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
    <script>Dropzone.autoDiscover = false;</script>
    <script src="{{ asset('admin-assets/js/gallery-photos.js') }}"></script>
@endsection
