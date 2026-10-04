<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\DTOs\Admin\RoleData;
use Spatie\Permission\Models\Role;

final readonly class SaveRoleAction
{
    public function handle(RoleData $data, ?Role $role = null): Role
    {
        $role ??= new Role(['guard_name' => 'web']);

        $role->name = $data->name;
        $role->login_enabled = $data->loginEnabled;
        $role->save();

        $role->syncPermissions($data->permissions);

        return $role;
    }
}
