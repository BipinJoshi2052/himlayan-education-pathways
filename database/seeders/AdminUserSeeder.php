<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Spatie caches the permission/role list; stale entries from an
        // earlier run (e.g. a deleted permission) cause FK violations below
        // if this isn't cleared first. Standard practice for a permission seeder.
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'manage-roles',
            'manage-sections',
            'manage-posts',
            'manage-services',
            'manage-galleries',
            'manage-inquiries',
            'manage-settings',
            'manage-notices',
            'manage-users',
            'manage-visits',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($permissions);

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin User', 'password' => 'password'],
        );

        $admin->assignRole($adminRole);
    }
}
