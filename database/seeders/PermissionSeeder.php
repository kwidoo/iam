<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Seed system permissions and assign them to roles.
     */
    public function run(): void
    {
        // Create permissions for each resource
        $resources = ['users', 'roles', 'permissions', 'organizations', 'settings'];
        $actions = ['view', 'create', 'edit', 'delete'];

        $allPermissions = [];

        // Create basic CRUD permissions
        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                $permissionName = "$action-$resource";

                // Create the permission if it doesn't exist
                Permission::updateOrCreate(
                    ['name' => $permissionName],
                    ['guard_name' => 'web']
                );

                $allPermissions[] = $permissionName;
            }
        }

        // Create special permissions that don't follow the standard pattern
        $specialPermissions = [
            'manage-system',
            'approve-registrations',
            'assign-roles',
            'view-audit-logs',
            'export-data',
            'import-data',
        ];

        foreach ($specialPermissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );

            $allPermissions[] = $permission;
        }

        // Assign permissions to roles
        $this->assignRolePermissions($allPermissions);
    }

    /**
     * Assign permissions to each role according to their level of access.
     *
     * @param array $allPermissions All available permissions
     */
    private function assignRolePermissions(array $allPermissions): void
    {
        // Get roles
        $superAdminRole = Role::findByName('super-admin');
        $adminRole = Role::findByName('admin');
        $managerRole = Role::findByName('manager');
        $staffRole = Role::findByName('staff');
        $userRole = Role::findByName('user');

        // Super admin gets all permissions implicitly
        // (often done through gating/policies rather than explicit permissions)

        // Admin gets most permissions except some system-critical ones
        $adminPermissions = array_filter($allPermissions, function ($permission) {
            return !in_array($permission, [
                'manage-system',
                'delete-organizations',
                'edit-organizations',
            ]);
        });
        $adminRole->syncPermissions($adminPermissions);

        // Manager gets view access to everything and edit access to most resources
        $managerPermissions = [
            'view-users',
            'view-roles',
            'view-permissions',
            'view-organizations',
            'view-settings',
            'create-users',
            'edit-users',
            'approve-registrations',
            'export-data',
        ];
        $managerRole->syncPermissions($managerPermissions);

        // Staff members get view access to many resources and edit access to basic user data
        $staffPermissions = [
            'view-users',
            'view-organizations',
            'view-settings',
            'export-data',
        ];
        $staffRole->syncPermissions($staffPermissions);

        // Standard users get minimal permissions
        $userPermissions = [
            'view-settings',
        ];
        $userRole->syncPermissions($userPermissions);
    }
}
