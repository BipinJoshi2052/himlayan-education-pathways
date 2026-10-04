@extends('layouts.admin')

@section('title', $role->exists ? 'Edit Role' : 'New Role')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">{{ $role->exists ? 'Edit Role' : 'New Role' }}</h4>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-link">&larr; Back to roles</a>
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
          action="{{ $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store') }}">
        @csrf
        @if ($role->exists)
            @method('PUT')
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Role Name</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $role->name) }}" required>
                </div>

                <div class="form-check mb-0">
                    <input type="checkbox" class="form-check-input" id="login_enabled" name="login_enabled" value="1"
                           @checked(old('login_enabled', $role->exists ? $role->login_enabled : true))>
                    <label class="form-check-label" for="login_enabled">Login enabled</label>
                    <div class="text-muted small">When off, any user whose only roles are this one can't log in — existing sessions stay active until they log out.</div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Permissions</h5></div>
            <div class="card-body">
                @php $rolePermissions = old('permissions', $role->exists ? $role->permissions->pluck('name')->all() : []); @endphp
                <div class="row">
                    @foreach ($allPermissions as $permission)
                        <div class="col-md-4 form-check mb-2">
                            <input type="checkbox" class="form-check-input" id="perm-{{ $permission->id }}"
                                   name="permissions[]" value="{{ $permission->name }}"
                                   @checked(in_array($permission->name, $rolePermissions, true))>
                            <label class="form-check-label" for="perm-{{ $permission->id }}">{{ $permission->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">{{ $role->exists ? 'Update Role' : 'Create Role' }}</button>
    </form>
@endsection
