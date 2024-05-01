<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Organization;

class OrganizationPolicy
{
    // Check if the user can view any organization
    public function viewAny(User $user)
    {
        // Assuming you want any authenticated user to view any organization listing
        return $user->hasRole('owner') || $user->hasPermissionTo('view organization');
    }

    // Check if the user can view a specific organization
    public function view(User $user, Organization $organization)
    {
        return $user->organizations->contains($organization->id) || $user->hasPermissionTo('view organization');
    }

    // Check if the user can create a new organization
    public function create(User $user)
    {
        // Assume only certain roles or the owner can create new organizations
        return $user->hasRole('owner') || $user->hasPermissionTo('create organization');
    }

    // Check if the user can update a specific organization
    public function update(User $user, Organization $organization)
    {
        // Checks if the user is part of this organization and if they have the role to update it
        return $user->organizations()->where('organization_id', $organization->id)->wherePivot('role', 'admin')->exists() || $user->hasPermissionTo('edit organization');
    }

    // Check if the user can delete a specific organization
    public function delete(User $user, Organization $organization)
    {
        // Checks if the user is the owner of the organization
        return $user->organizations()->where('organization_id', $organization->id)->wherePivot('role', 'owner')->exists() || $user->hasPermissionTo('delete organization');
    }
}
