<?php

namespace App\Contracts\Services\Organizations;

use Kwidoo\Mere\Contracts\Models\OrganizationInterface;
use Kwidoo\Mere\Contracts\Models\UserInterface;
use Spatie\Permission\Models\Role;

/**
 * Interface for organization access management operations.
 */
interface OrganizationAccessService
{
    /**
     * Sync user roles based on their organization role.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Organization $organization
     * @return void
     */
    public function syncUserOrganizationRoles(UserInterface $user, OrganizationInterface $organization): void;

    /**
     * Sync organization-specific permissions for a role.
     *
     * @param \Spatie\Permission\Models\Role $role
     * @param \App\Models\Organization $organization
     * @param array $resources Resources to generate permissions for (e.g., ['user', 'profile'])
     * @param array $actions Actions to generate permissions for (e.g., ['view', 'create'])
     * @return void
     */
    public function syncRolePermissions(Role $role, OrganizationInterface $organization, array $resources, array $actions): void;

    /**
     * Set up default permissions for standard roles in an organization.
     *
     * @param \App\Models\Organization $organization
     * @return void
     */
    public function setupOrganizationDefaultPermissions(OrganizationInterface $organization): void;

    /**
     * Sync all users' roles and permissions in an organization.
     *
     * @param \App\Models\Organization $organization
     * @return void
     */
    public function syncOrganizationUsers(OrganizationInterface $organization): void;
}
