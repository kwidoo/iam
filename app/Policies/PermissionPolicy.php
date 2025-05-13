<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Permission;

class PermissionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any permissions.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        // Only super-admin or users with 'view' permissions on permissions can list them
        return $user->hasRole('super-admin') || $user->hasPermissionTo('org-*-permission.view');
    }

    /**
     * Determine whether the user can view a specific permission.
     *
     * @param User $user
     * @param Permission $permission
     * @return bool
     */
    public function view(User $user, Permission $permission): bool
    {
        // Extract organization ID if this is an organization-specific permission
        if (preg_match('/^org-([a-zA-Z0-9\-]+)-/', $permission->name, $matches)) {
            $orgId = $matches[1];
            return $user->hasRole('super-admin') || $user->hasPermissionTo("org-{$orgId}-permission.view");
        }

        // For non-organization specific permissions, only super-admin can manage
        return $user->hasRole('super-admin');
    }

    /**
     * Determine whether the user can create permissions.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        // Only super-admin or organization admins can create permissions
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Check if user has any create permission for any organization
        foreach ($user->organizations as $organization) {
            if ($user->hasPermissionTo("org-{$organization->id}-permission.create")) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can update a permission.
     *
     * @param User $user
     * @param Permission $permission
     * @return bool
     */
    public function update(User $user, Permission $permission): bool
    {
        // Extract organization ID if this is an organization-specific permission
        if (preg_match('/^org-([a-zA-Z0-9\-]+)-/', $permission->name, $matches)) {
            $orgId = $matches[1];
            return $user->hasRole('super-admin') || $user->hasPermissionTo("org-{$orgId}-permission.edit");
        }

        // For non-organization specific permissions, only super-admin can manage
        return $user->hasRole('super-admin');
    }

    /**
     * Determine whether the user can delete a permission.
     *
     * @param User $user
     * @param Permission $permission
     * @return bool
     */
    public function delete(User $user, Permission $permission): bool
    {
        // Extract organization ID if this is an organization-specific permission
        if (preg_match('/^org-([a-zA-Z0-9\-]+)-/', $permission->name, $matches)) {
            $orgId = $matches[1];
            return $user->hasRole('super-admin') || $user->hasPermissionTo("org-{$orgId}-permission.delete");
        }

        // For non-organization specific permissions, only super-admin can manage
        return $user->hasRole('super-admin');
    }
}
