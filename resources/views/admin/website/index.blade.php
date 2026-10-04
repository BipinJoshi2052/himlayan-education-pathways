@extends('layouts.admin')

@section('title', 'Website')

@php
    $activeTab = request('tab', 'hero');
    $tabs = [
        'hero' => 'Hero',
    ];
@endphp

@section('content')
    <h4 class="mb-4">Website</h4>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <ul class="nav nav-tabs mb-4" id="website-tabs" role="tablist">
        @foreach ($tabs as $key => $label)
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTab === $key ? 'active' : '' }}" id="{{ $key }}-tab"
                        data-bs-toggle="tab" data-bs-target="#{{ $key }}-pane" type="button" role="tab">
                    {{ $label }}
                </button>
            </li>
        @endforeach
    </ul>

    <div class="tab-content" id="website-tabs-content">
        <div class="tab-pane fade {{ $activeTab === 'hero' ? 'show active' : '' }}" id="hero-pane" role="tabpanel">
            @include('admin.website.hero', ['heroLayout' => $heroLayout])
        </div>
    </div>
@endsection
