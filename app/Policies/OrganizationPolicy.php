<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any organizations.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        // Super-admin can view all organizations
        return $user->hasRole('super-admin');
    }

    /**
     * Determine whether the user can view the organization.
     *
     * @param User $user
     * @param Organization $organization
     * @return bool
     */
    public function view(User $user, Organization $organization): bool
    {
        // Super-admin can view any organization
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Users can view organizations they belong to
        if ($user->organizations->contains($organization)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create organizations.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        // Super-admin can create organizations, and regular users too if enabled in config
        return $user->hasRole('super-admin') ||
            config('iam.allow_user_organization_creation', false);
    }

    /**
     * Determine whether the user can update the organization.
     *
     * @param User $user
     * @param Organization $organization
     * @return bool
     */
    public function update(User $user, Organization $organization): bool
    {
        // Super-admin can update any organization
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Organization owner can update it
        if ($organization->owner_id === $user->id) {
            return true;
        }

        // Users with organization-specific admin permissions can update it
        if (
            $user->hasOrganizationRole('admin', $organization) ||
            $user->hasOrganizationPermission('organization.edit', $organization->id)
        ) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the organization.
     *
     * @param User $user
     * @param Organization $organization
     * @return bool
     */
    public function delete(User $user, Organization $organization): bool
    {
        // Only super-admin and the organization owner can delete an organization
        return $user->hasRole('super-admin') || $organization->owner_id === $user->id;
    }

    /**
     * Determine whether the user can manage users within the organization.
     *
     * @param User $user
     * @param Organization $organization
     * @return bool
     */
    public function manageUsers(User $user, Organization $organization): bool
    {
        // Super-admin can manage users in any organization
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Organization owner and admins can manage users
        if (
            $organization->owner_id === $user->id ||
            $user->hasOrganizationRole('admin', $organization) ||
            $user->hasOrganizationPermission('user.edit', $organization->id)
        ) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can manage roles within the organization.
     *
     * @param User $user
     * @param Organization $organization
     * @return bool
     */
    public function manageRoles(User $user, Organization $organization): bool
    {
        // Super-admin can manage roles in any organization
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Organization owner and admins can manage roles
        if (
            $organization->owner_id === $user->id ||
            $user->hasOrganizationRole('admin', $organization) ||
            $user->hasOrganizationPermission('role.edit', $organization->id)
        ) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can manage permissions within the organization.
     *
     * @param User $user
     * @param Organization $organization
     * @return bool
     */
    public function managePermissions(User $user, Organization $organization): bool
    {
        // Super-admin can manage permissions in any organization
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Organization owner and admins can manage permissions
        if (
            $organization->owner_id === $user->id ||
            $user->hasOrganizationRole('admin', $organization) ||
            $user->hasOrganizationPermission('permission.edit', $organization->id)
        ) {
            return true;
        }

        return false;
    }
}
