<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizationPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Organization $organization)
    {
        // Allow if the user is a member of the organization.
        return $organization->users()->where('user_id', $user->id)->exists();
    }

    public function update(User $user, Organization $organization)
    {
        // Only owners and admins can update.
        $membership = $organization->users()->where('user_id', $user->id)->first();
        return $membership && in_array($membership->pivot->role, ['owner', 'admin']);
    }

    public function delete(User $user, Organization $organization)
    {
        // Only an owner can delete; additional checks for sole ownership are handled in the controller.
        $membership = $organization->users()->where('user_id', $user->id)->first();
        return $membership && $membership->pivot->role === 'owner';
    }

    public function invite(User $user, Organization $organization)
    {
        // Only owners and admins can invite new users.
        $membership = $organization->users()->where('user_id', $user->id)->first();
        return $membership && in_array($membership->pivot->role, ['owner', 'admin']);
    }
}
