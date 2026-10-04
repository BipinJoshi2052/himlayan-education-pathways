@extends('layouts.admin')

@section('title', 'Edit Section')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Edit Section — {{ $section->page_slug }} / {{ $section->key }}</h4>
        <a href="{{ route('admin.sections.index') }}" class="btn btn-link">&larr; Back to sections</a>
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

    <form method="POST" action="{{ route('admin.sections.update', $section) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.sections._form')
    </form>
@endsection
