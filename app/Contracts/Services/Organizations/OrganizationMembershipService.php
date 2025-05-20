<?php

namespace App\Contracts\Services\Organizations;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection; // Added this line

interface OrganizationMembershipService
{
    /**
     * Adds a user to an organization with a specific role.
     *
     * @param Organization $organization The organization.
     * @param User $user The user to add.
     * @param string $role The role to assign to the user in the organization (e.g., 'owner', 'admin', 'member').
     * @return Model Returns the organization user pivot model or relevant model.
     * @throws \Illuminate\Validation\ValidationException If user is already a member or other validation fails.
     * @throws \Throwable For other errors.
     */
    public function addUserToOrganization(Organization $organization, User $user, string $role): Model;

    /**
     * Removes a user from an organization.
     *
     * @param Organization $organization The organization.
     * @param User $user The user to remove.
     * @return void
     * @throws \Illuminate\Validation\ValidationException Throws ValidationException for specific errors like removing sole owner (or a custom exception).
     * @throws \Throwable For other errors.
     */
    public function removeUserFromOrganization(Organization $organization, User $user): void;

    /**
     * Updates a user's role within an organization.
     *
     * @param Organization $organization The organization.
     * @param User $user The user whose role is to be updated.
     * @param string $newRole The new role for the user.
     * @return Model Returns the organization user pivot model or relevant model.
     * @throws \Illuminate\Validation\ValidationException If user is not a member or other validation fails (or a custom exception for specific errors).
     * @throws \Throwable For other errors.
     */
    public function updateUserRoleInOrganization(Organization $organization, User $user, string $newRole): Model;

    /**
     * Retrieves roles specific to an organization based on naming convention.
     *
     * @param Organization $organization The organization.
     * @return Collection A collection of roles.
     */
    public function getRolesForOrganization(Organization $organization): Collection;

    /**
     * Retrieves permissions specific to an organization based on naming convention.
     *
     * @param Organization $organization The organization.
     * @return Collection A collection of permissions.
     */
    public function getPermissionsForOrganization(Organization $organization): Collection;
}
