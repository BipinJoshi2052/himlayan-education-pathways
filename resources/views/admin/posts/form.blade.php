@extends('layouts.admin')

@section('title', $post->exists ? 'Edit Post' : 'New Post')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">{{ $post->exists ? 'Edit Post' : 'New Post' }}</h4>
        <a href="{{ route('admin.posts.index') }}" class="btn btn-link">&larr; Back to posts</a>
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
          action="{{ $post->exists ? route('admin.posts.update', $post) : route('admin.posts.store') }}"
          enctype="multipart/form-data">
        @csrf
        @if ($post->exists)
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
                                           value="{{ old('title.'.$locale, $post->getTranslation('title', $locale, false)) }}"
                                           @if ($locale === 'en') required @endif>
                                </div>
                            @endforeach
                        </div>

                        <div class="form-group">
                            <label class="form-label">Summary</label>
                            @foreach ($locales as $i => $locale)
                                <div data-locale-pane="{{ $locale }}" style="{{ $i === 0 ? '' : 'display:none' }}">
                                    <textarea class="form-control mb-2" rows="2" name="summary[{{ $locale }}]">{{ old('summary.'.$locale, $post->getTranslation('summary', $locale, false)) }}</textarea>
                                </div>
                            @endforeach
                        </div>

                        @include('components.admin.rich-text', [
                            'name' => 'content', 'model' => $post, 'locales' => $locales, 'label' => 'Content',
                        ])
                    </div>
                </div>

                @include('components.admin.seo-inputs', ['model' => $post, 'locales' => $locales])
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">Publishing</h5></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Type</label>
                            <select class="form-select" name="type" required>
                                @foreach ($types as $type)
                                    <option value="{{ $type->value }}" @selected(old('type', $post->type?->value) === $type->value)>{{ $type->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->value }}" @selected(old('status', $post->status?->value ?? 'draft') === $status->value)>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Published Date</label>
                            <input type="datetime-local" class="form-control" name="published_at"
                                   value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}">
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1"
                                   @checked(old('is_featured', $post->is_featured))>
                            <label class="form-check-label" for="is_featured">Featured</label>
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label">Sort order</label>
                            <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', $post->sort_order ?? 0) }}">
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">Featured Image</h5></div>
                    <div class="card-body">
                        @if ($post->exists && $post->getFirstMediaUrl('featured_image', 'thumb'))
                            @include('admin.partials.existing-image', [
                                'url' => $post->getFirstMediaUrl('featured_image', 'thumb'),
                                'alt' => 'Current featured image',
                                'removeName' => 'remove_featured_image',
                                'style' => 'max-height: 200px;',
                            ])
                        @endif
                        <input type="file" class="form-control" name="featured_image" accept="image/*">
                        <small class="text-muted">JPEG/PNG/WebP, max 5MB. Auto-generates 300&times;200 and 800&times;500 WebP conversions.</small>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">{{ $post->exists ? 'Update Post' : 'Create Post' }}</button>
            </div>
        </div>
    </form>
@endsection
