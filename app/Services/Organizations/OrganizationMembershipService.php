<?php

namespace App\Services\Organizations;

use App\Contracts\Services\Organizations\OrganizationMembershipService as OrganizationMembershipServiceInterface;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Validation\ValidationException;
use App\Events\OrganizationMembershipChanged;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection;
use Kwidoo\Lifecycle\Traits\RunsLifecycle; // Added for lifecycle
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle as LifecycleContract; // Added for lifecycle

class OrganizationMembershipService implements OrganizationMembershipServiceInterface
{
    use RunsLifecycle; // Added for lifecycle

    protected LifecycleContract $lifecycle; // Added for lifecycle

    public function __construct(LifecycleContract $lifecycle) // Added for lifecycle
    {
        $this->lifecycle = $lifecycle;
    }

    protected function eventKey(): string // Added for lifecycle
    {
        return 'organization.membership';
    }

    public function addUserToOrganization(Organization $organization, User $user, string $role): Model
    {
        $contextData = [
            'organization' => $organization,
            'user' => $user,
            'role' => $role,
        ];

        return $this->runLifecycle(
            context: $contextData,
            callback: fn() => $this->handleAddUserToOrganization($organization, $user, $role)
        );
    }

    protected function handleAddUserToOrganization(Organization $organization, User $user, string $role): Model
    {
        // Check if user is already in the organization
        if ($organization->users()->where('user_id', $user->id)->exists()) {
            throw ValidationException::withMessages([
                'user_id' => 'User is already a member of this organization'
            ]);
        }

        // Add user to organization with specified role
        $organization->users()->attach($user->id, ['role' => $role]);

        // Assign corresponding Spatie role
        $roleName = "{$organization->slug}-{$role}";
        $spatieRole = SpatieRole::firstOrCreate([
            'name' => $roleName,
            'guard_name' => 'web'
        ]);

        // Sync the user's role
        $user->assignRole($spatieRole);

        // Dispatch event to trigger role permissions sync
        Event::dispatch(new OrganizationMembershipChanged($organization, $user));

        return $organization->users()->where('user_id', $user->id)->firstOrFail();
    }

    public function removeUserFromOrganization(Organization $organization, User $user): void
    {
        $contextData = [
            'organization' => $organization,
            'user' => $user,
        ];

        $this->runLifecycle(
            context: $contextData,
            callback: fn() => $this->handleRemoveUserFromOrganization($organization, $user)
        );
    }

    protected function handleRemoveUserFromOrganization(Organization $organization, User $user): void
    {
        // Sole owner check
        $owners = $organization->users()->wherePivot('role', 'owner')->count();
        if ($owners <= 1 && $organization->users()->where('user_id', $user->id)->wherePivot('role', 'owner')->exists()) {
            throw ValidationException::withMessages(['user_id' => 'Cannot remove the sole owner of the organization']);
        }

        // Get the user's current role in the organization from the pivot table
        $orgUser = $organization->users()->where('user_id', $user->id)->first();
        $orgUserRole = $orgUser ? $orgUser->pivot->role : null;

        // Remove user from organization
        $organization->users()->detach($user->id);

        // Remove organization-specific role if it exists
        if ($orgUserRole) {
            $roleName = "{$organization->slug}-{$orgUserRole}";
            $user->removeRole($roleName);
        }

        // Dispatch event to trigger role permissions sync
        Event::dispatch(new OrganizationMembershipChanged($organization, $user));
    }

    public function updateUserRoleInOrganization(Organization $organization, User $user, string $newRole): Model
    {
        $contextData = [
            'organization' => $organization,
            'user' => $user,
            'newRole' => $newRole,
            'oldRole' => $organization->users()->where('user_id', $user->id)->first()?->pivot->role
        ];

        return $this->runLifecycle(
            context: $contextData,
            callback: fn() => $this->handleUpdateUserRoleInOrganization($organization, $user, $newRole)
        );
    }

    protected function handleUpdateUserRoleInOrganization(Organization $organization, User $user, string $newRole): Model
    {
        // Check if user is a member
        $orgUser = $organization->users()->where('user_id', $user->id)->first();
        if (!$orgUser) {
            throw ValidationException::withMessages([
                'user_id' => 'User is not a member of this organization'
            ]);
        }

        // Sole owner check
        $owners = $organization->users()->wherePivot('role', 'owner')->count();
        if (
            $owners <= 1 &&
            $orgUser->pivot->role === 'owner' &&
            $newRole !== 'owner'
        ) {
            throw ValidationException::withMessages(['user_id' => 'Cannot change role of the sole owner of the organization']);
        }

        // Get old Spatie role name
        $oldSpatieRoleName = "{$organization->slug}-{$orgUser->pivot->role}";

        // Update user's organization role
        $organization->users()->updateExistingPivot($user->id, ['role' => $newRole]);

        // Construct new Spatie role name
        $newSpatieRoleName = "{$organization->slug}-{$newRole}";
        $newSpatieRole = SpatieRole::firstOrCreate([
            'name' => $newSpatieRoleName,
            'guard_name' => 'web'
        ]);

        // Remove old role and assign new role
        if ($oldSpatieRoleName !== $newSpatieRoleName) { // Only if role actually changed
            $user->removeRole($oldSpatieRoleName);
            $user->assignRole($newSpatieRole);
        }


        // Dispatch event to trigger role permissions sync
        Event::dispatch(new OrganizationMembershipChanged($organization, $user));

        return $organization->users()->where('user_id', $user->id)->firstOrFail();
    }

    // Helper methods (e.g., for Spatie role name generation) can be added later.

    public function getRolesForOrganization(Organization $organization): Collection
    {
        // Logic from OrganizationController's getRoles method
        return SpatieRole::where('name', 'like', "{$organization->slug}-%")->get();
    }

    public function getPermissionsForOrganization(Organization $organization): Collection
    {
        // Logic from OrganizationController's getPermissions method
        // Assuming Permission model is Spatie\Permission\Models\Permission
        return Permission::where('name', 'like', "org-{$organization->id}-%")->get();
    }
}
