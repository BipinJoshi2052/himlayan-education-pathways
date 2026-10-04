@extends('layouts.admin')

@section('title', 'Roles & Permissions')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">Roles & Permissions</h4>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">+ New Role</a>
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

    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Permissions</th>
                        <th>Users</th>
                        <th>Login</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>
                            <td>{{ $role->permissions->count() }}</td>
                            <td>{{ $role->users_count }}</td>
                            <td>
                                @if ($role->login_enabled)
                                    <span class="badge bg-success-subtle text-success">Enabled</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Disabled</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-soft-primary">Edit</a>
                                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="d-inline" data-confirm="Delete this role?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-soft-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No roles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
