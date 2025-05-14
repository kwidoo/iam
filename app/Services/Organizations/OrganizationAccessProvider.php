<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class OrganizationAccessProvider
{
    /**
     * Sync user roles based on their organization role.
     *
     * @param User $user
     * @param Organization $organization
     * @return void
     */
    public function syncUserOrganizationRoles(User $user, Organization $organization): void
    {
        // Get the user's role in the organization from the pivot table
        $pivotRole = $user->organizations()
            ->where('organization_id', $organization->id)
            ->first()?->pivot->role;

        if (!$pivotRole) {
            return;
        }

        // Format role name as orgSlug-roleName (e.g., acme-admin)
        $roleName = "{$organization->slug}-{$pivotRole}";

        // Check if the role exists, create it if not
        $role = Role::firstOrCreate([
            'name' => $roleName,
            'guard_name' => 'web'
        ]);

        // Assign the role to the user with organization context if not already assigned
        if (!$user->hasRole($roleName)) {
            $user->assignRole($role);
        }
    }

    /**
     * Sync organization-specific permissions for a role.
     *
     * @param Role $role
     * @param Organization $organization
     * @param array $resources Resources to generate permissions for (e.g., ['user', 'profile'])
     * @param array $actions Actions to generate permissions for (e.g., ['view', 'create'])
     * @return void
     */
    public function syncRolePermissions(Role $role, Organization $organization, array $resources, array $actions): void
    {
        $permissions = [];

        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                $permissionName = "org-{$organization->id}-{$resource}.{$action}";

                // Create the permission if it doesn't exist
                $permission = Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web'
                ]);

                $permissions[] = $permission->id;
            }
        }

        // Sync permissions to the role
        $role->syncPermissions($permissions);
    }

    /**
     * Set up default permissions for standard roles in an organization.
     *
     * @param Organization $organization
     * @return void
     */
    public function setupOrganizationDefaultPermissions(Organization $organization): void
    {
        // Define resources and their available actions
        $resources = [
            'user' => ['view', 'list', 'create', 'edit', 'delete'],
            'profile' => ['view', 'edit'],
            'organization' => ['view', 'edit'],
            'role' => ['view', 'list', 'create', 'edit', 'delete'],
            'permission' => ['view', 'list', 'create', 'edit', 'delete'],
        ];

        // Set up admin role with all permissions
        $adminRole = Role::firstOrCreate([
            'name' => "{$organization->slug}-admin",
            'guard_name' => 'web'
        ]);

        $ownerRole = Role::firstOrCreate([
            'name' => "{$organization->slug}-owner",
            'guard_name' => 'web'
        ]);

        $memberRole = Role::firstOrCreate([
            'name' => "{$organization->slug}-member",
            'guard_name' => 'web'
        ]);

        // Admin gets all permissions
        $this->syncRolePermissions(
            $adminRole,
            $organization,
            array_keys($resources),
            array_unique(array_merge(...array_values($resources)))
        );

        // Owner gets all permissions (same as admin)
        $this->syncRolePermissions(
            $ownerRole,
            $organization,
            array_keys($resources),
            array_unique(array_merge(...array_values($resources)))
        );

        // Member gets only view/list permissions
        $this->syncRolePermissions(
            $memberRole,
            $organization,
            array_keys($resources),
            ['view', 'list']
        );
    }

    /**
     * Sync all users' roles and permissions in an organization.
     *
     * @param Organization $organization
     * @return void
     */
    public function syncOrganizationUsers(Organization $organization): void
    {
        $this->setupOrganizationDefaultPermissions($organization);

        // Get all users in the organization
        $users = $organization->users;

        foreach ($users as $user) {
            $this->syncUserOrganizationRoles($user, $organization);
        }
    }
}
