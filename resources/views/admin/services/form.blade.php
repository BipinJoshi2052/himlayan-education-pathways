@extends('layouts.admin')

@section('title', $service->exists ? 'Edit Service' : 'New Service')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">{{ $service->exists ? 'Edit Service' : 'New Service' }}</h4>
        <a href="{{ route('admin.services.index') }}" class="btn btn-link">&larr; Back to services</a>
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
          action="{{ $service->exists ? route('admin.services.update', $service) : route('admin.services.store') }}"
          enctype="multipart/form-data">
        @csrf
        @if ($service->exists)
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
                                           value="{{ old('title.'.$locale, $service->getTranslation('title', $locale, false)) }}"
                                           @if ($locale === 'en') required @endif>
                                </div>
                            @endforeach
                        </div>

                        <div class="form-group">
                            <label class="form-label">Summary</label>
                            @foreach ($locales as $i => $locale)
                                <div data-locale-pane="{{ $locale }}" style="{{ $i === 0 ? '' : 'display:none' }}">
                                    <textarea class="form-control mb-2" rows="2" name="summary[{{ $locale }}]">{{ old('summary.'.$locale, $service->getTranslation('summary', $locale, false)) }}</textarea>
                                </div>
                            @endforeach
                        </div>

                        @include('components.admin.rich-text', [
                            'name' => 'description', 'model' => $service, 'locales' => $locales, 'label' => 'Curriculum / Description',
                        ])
                    </div>
                </div>

                @include('components.admin.seo-inputs', ['model' => $service, 'locales' => $locales])
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">Details</h5></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Duration</label>
                            <input type="text" class="form-control" name="duration" placeholder="e.g. 6 Weeks"
                                   value="{{ old('duration', $service->duration) }}">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Fee</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="fee"
                                   value="{{ old('fee', $service->fee) }}">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Icon</label>
                            <input type="text" class="form-control" name="icon" placeholder="e.g. bi-book or an SVG string"
                                   value="{{ old('icon', $service->icon) }}">
                            <small class="text-muted">A Bootstrap Icons class name or a raw SVG string — rendered as-is on the public site.</small>
                        </div>

                        <div class="form-check mb-2">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                   @checked(old('is_active', $service->exists ? $service->is_active : true))>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1"
                                   @checked(old('is_featured', $service->is_featured))>
                            <label class="form-check-label" for="is_featured">Featured</label>
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label">Sort order</label>
                            <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', $service->sort_order ?? 0) }}">
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">Media</h5></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Cover Image</label>
                            @if ($service->exists && $service->getFirstMediaUrl('cover_image'))
                                <img src="{{ $service->getFirstMediaUrl('cover_image') }}" class="img-fluid rounded mb-2" alt="Current cover">
                            @endif
                            <input type="file" class="form-control" name="cover_image" accept="image/*">
                        </div>
                        <div class="form-group mb-0">
                            <label class="form-label">Brochure (PDF)</label>
                            @if ($service->exists && $service->getFirstMedia('brochure_pdf'))
                                <p class="mb-2"><a href="{{ $service->getFirstMediaUrl('brochure_pdf') }}" target="_blank">Current brochure &rarr;</a></p>
                            @endif
                            <input type="file" class="form-control" name="brochure_pdf" accept="application/pdf">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">{{ $service->exists ? 'Update Service' : 'Create Service' }}</button>
            </div>
        </div>
    </form>
@endsection
