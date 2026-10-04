<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\SaveRoleAction;
use App\DTOs\Admin\RoleData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

final class RoleController extends Controller
{
    public function index(): View
    {
        return view('admin.roles.index', [
            'roles' => Role::withCount('users')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.roles.form', [
            'role' => new Role,
            'allPermissions' => Permission::orderBy('name')->get(),
        ]);
    }

    public function store(RoleRequest $request, SaveRoleAction $action): RedirectResponse
    {
        $role = $action->handle(RoleData::fromArray($request->validated()));

        return redirect()->route('admin.roles.edit', $role)->with('status', 'Role created.');
    }

    public function edit(Role $role): View
    {
        return view('admin.roles.form', [
            'role' => $role,
            'allPermissions' => Permission::orderBy('name')->get(),
        ]);
    }

    public function update(RoleRequest $request, Role $role, SaveRoleAction $action): RedirectResponse
    {
        $data = RoleData::fromArray($request->validated());

        // Disabling login on the only role that lets the CURRENTLY logged-in
        // admin in would lock them out immediately — block that specific
        // case rather than letting it happen silently.
        if (! $data->loginEnabled && auth()->user()?->hasRole($role->name)) {
            $otherEnabledRoles = auth()->user()->roles()
                ->where('id', '!=', $role->id)
                ->where('login_enabled', true)
                ->exists();

            if (! $otherEnabledRoles) {
                return back()->withErrors([
                    'login_enabled' => "You can't disable login for this role — it's the only role giving your own account login access.",
                ])->withInput();
            }
        }

        $action->handle($data, $role);

        return redirect()->route('admin.roles.edit', $role)->with('status', 'Role updated.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->users()->exists()) {
            return back()->withErrors([
                'role' => 'This role still has users assigned — reassign or remove them first.',
            ]);
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('status', 'Role deleted.');
    }
}
