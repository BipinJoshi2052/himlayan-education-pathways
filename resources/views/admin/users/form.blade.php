@extends('layouts.admin')

@section('title', $user->exists ? 'Edit User' : 'New User')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">{{ $user->exists ? 'Edit User' : 'New User' }}</h4>
        <a href="{{ route('admin.users.index') }}" class="btn btn-link">&larr; Back to users</a>
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
          action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}">
        @csrf
        @if ($user->exists)
            @method('PUT')
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" required>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-0">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" autocomplete="new-password">
                    @if ($user->exists)
                        <small class="text-muted">Leave blank to keep the current password.</small>
                    @endif
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Roles</h5></div>
            <div class="card-body">
                @php $userRoles = old('roles', $user->exists ? $user->roles->pluck('name')->all() : []); @endphp
                @forelse ($allRoles as $role)
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="role-{{ $role->id }}"
                               name="roles[]" value="{{ $role->name }}"
                               @checked(in_array($role->name, $userRoles, true))>
                        <label class="form-check-label" for="role-{{ $role->id }}">{{ $role->name }}</label>
                    </div>
                @empty
                    <p class="text-muted mb-0">No roles exist yet — <a href="{{ route('admin.roles.create') }}">create one</a> first.</p>
                @endforelse
            </div>
        </div>

        <button type="submit" class="btn btn-primary">{{ $user->exists ? 'Update User' : 'Create User' }}</button>
    </form>
@endsection
