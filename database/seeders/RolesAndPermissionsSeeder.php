<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Optional: clear cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [
            'super admin',
            'admin',
            'user',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Optional: Create permissions here and assign to roles
        // Example:
        // $permission = Permission::firstOrCreate(['name' => 'manage users']);
        // $role = Role::where('name', 'admin')->first();
        // $role->givePermissionTo($permission);
    }
}
