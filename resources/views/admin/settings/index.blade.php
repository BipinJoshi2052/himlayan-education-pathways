@extends('layouts.admin')

@section('title', 'Settings')

@php
    $activeTab = request('tab', 'general');
    $tabs = [
        'general' => 'General',
        'seo' => 'SEO & Social',
        'smtp' => 'SMTP / Mail',
        'appearance' => 'Appearance',
        'languages' => 'Languages',
        'loader' => 'Loader',
        'widgets' => 'Widgets',
    ];
@endphp

@section('content')
    <h4 class="mb-4">Settings</h4>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <ul class="nav nav-tabs mb-4" id="settings-tabs" role="tablist">
        @foreach ($tabs as $key => $label)
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTab === $key ? 'active' : '' }}" id="{{ $key }}-tab"
                        data-bs-toggle="tab" data-bs-target="#{{ $key }}-pane" type="button" role="tab">
                    {{ $label }}
                </button>
            </li>
        @endforeach
    </ul>

    <div class="tab-content" id="settings-tabs-content">
        <div class="tab-pane fade {{ $activeTab === 'general' ? 'show active' : '' }}" id="general-pane" role="tabpanel">
            @include('admin.settings.general')
        </div>
        <div class="tab-pane fade {{ $activeTab === 'seo' ? 'show active' : '' }}" id="seo-pane" role="tabpanel">
            @include('admin.settings.seo', ['locales' => $locales])
            @include('admin.settings.social')
        </div>
        <div class="tab-pane fade {{ $activeTab === 'smtp' ? 'show active' : '' }}" id="smtp-pane" role="tabpanel">
            @include('admin.settings.smtp')
        </div>
        <div class="tab-pane fade {{ $activeTab === 'appearance' ? 'show active' : '' }}" id="appearance-pane" role="tabpanel">
            @include('admin.settings.appearance')
        </div>
        <div class="tab-pane fade {{ $activeTab === 'languages' ? 'show active' : '' }}" id="languages-pane" role="tabpanel">
            @include('admin.settings.languages', ['allLocales' => $allLocales])
        </div>
        <div class="tab-pane fade {{ $activeTab === 'loader' ? 'show active' : '' }}" id="loader-pane" role="tabpanel">
            @include('admin.settings.loader')
        </div>
        <div class="tab-pane fade {{ $activeTab === 'widgets' ? 'show active' : '' }}" id="widgets-pane" role="tabpanel">
            @include('admin.settings.widgets')
        </div>
    </div>
@endsection
