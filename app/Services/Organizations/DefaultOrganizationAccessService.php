<?php

namespace App\Services\Organizations;

use App\Contracts\Repositories\PermissionRepository;
use App\Contracts\Repositories\RoleRepository;
use App\Contracts\Services\Organizations\OrganizationAccessService;
use Kwidoo\Mere\Contracts\Models\OrganizationInterface;
use App\Services\BaseService;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;
use Kwidoo\Lifecycle\Traits\RunsLifecycle;
use Kwidoo\Mere\Contracts\Models\UserInterface;
use Kwidoo\Mere\Contracts\Services\MenuService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DefaultOrganizationAccessService extends BaseService implements OrganizationAccessService
{
    use RunsLifecycle;

    public function __construct(
        MenuService $menuService,
        RoleRepository $repository,
        Lifecycle $lifecycle,
        protected PermissionRepository $permissionRepository
    ) {
        parent::__construct($menuService, $repository, $lifecycle);
    }

    /**
     * Sync user roles based on their organization role.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Organization $organization
     * @return void
     */
    public function syncUserOrganizationRoles(UserInterface $user, OrganizationInterface $organization): void
    {
        $this->runLifecycle(
            context: ['user' => $user, 'organization' => $organization],
            callback: function () use ($user, $organization) {
                $orgRoles = $user->roles()
                    ->where('guard_name', 'web')
                    ->where('organization_id', $organization->id)
                    ->get();

                foreach ($orgRoles as $role) {
                    $user->assignRole($role);
                }
            }
        );
    }

    /**
     * Sync organization-specific permissions for a role.
     *
     * @param \Spatie\Permission\Models\Role $role
     * @param \App\Models\Organization $organization
     * @param array $resources Resources to generate permissions for (e.g., ['user', 'profile'])
     * @param array $actions Actions to generate permissions for (e.g., ['view', 'create'])
     * @return void
     */
    public function syncRolePermissions(Role $role, OrganizationInterface $organization, array $resources, array $actions): void
    {
        $this->runLifecycle(
            context: ['role' => $role, 'organization' => $organization],
            callback: function () use ($role, $organization, $resources, $actions) {
                $permissionNames = [];

                foreach ($resources as $resource) {
                    foreach ($actions as $action) {
                        $permissionNames[] = "{$action}_{$resource}";
                    }
                }

                // Exception for Spatie permissions as noted in guidelines
                $permissions = Permission::whereIn('name', $permissionNames)
                    ->where('guard_name', 'web')
                    ->where('organization_id', $organization->getId())
                    ->get();

                $role->syncPermissions($permissions);
            }
        );
    }

    /**
     * Set up default permissions for standard roles in an organization.
     *
     * @param \App\Models\Organization $organization
     * @return void
     */
    public function setupOrganizationDefaultPermissions(OrganizationInterface $organization): void
    {
        $this->runLifecycle(
            context: ['organization' => $organization],
            callback: function () use ($organization) {
                // Exception for Spatie permissions as noted in guidelines
                $adminRole = Role::where('name', 'admin')
                    ->where('organization_id', $organization->getId())
                    ->first();

                if ($adminRole) {
                    $this->syncRolePermissions(
                        $adminRole,
                        $organization,
                        ['user', 'profile', 'organization', 'role', 'permission'],
                        ['view', 'create', 'update', 'delete']
                    );
                }

                // Exception for Spatie permissions as noted in guidelines
                $userRole = Role::where('name', 'user')
                    ->where('organization_id', $organization->getId())
                    ->first();

                if ($userRole) {
                    $this->syncRolePermissions(
                        $userRole,
                        $organization,
                        ['profile'],
                        ['view', 'update']
                    );
                }
            }
        );
    }

    /**
     * Sync all users' roles and permissions in an organization.
     *
     * @param \App\Models\Organization $organization
     * @return void
     */
    public function syncOrganizationUsers(OrganizationInterface $organization): void
    {
        $this->runLifecycle(
            context: ['organization' => $organization],
            callback: function () use ($organization) {
                foreach ($organization->users as $user) {
                    $this->syncUserOrganizationRoles($user, $organization);
                }
            }
        );
    }

    protected function eventKey(): string
    {
        return 'organization_access';
    }
}
