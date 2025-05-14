<?php

namespace App\Http\Controllers;

use App\Events\OrganizationMembershipChanged;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Kwidoo\Mere\Data\ListQueryData;
use Kwidoo\Mere\Http\Controllers\ResourceController;
use Kwidoo\Mere\Http\Resources\ResourceCollection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class OrganizationController extends ResourceController
{
    public function index(ListQueryData $data): ResourceCollection
    {
        $data->resource = 'organization';
        return parent::index($data);
    }

    /**
     * Get organizations for the authenticated user
     *
     * @return JsonResponse
     */
    public function getUserOrganizations(): JsonResponse
    {
        $user = Auth::user();
        $organizations = $user->organizations;

        return response()->json(['data' => $organizations]);
    }

    /**
     * List users belonging to the organization
     *
     * @param Organization $organization
     * @return JsonResponse
     */
    public function getUsers(Organization $organization): JsonResponse
    {
        $this->authorize('view', $organization);

        $users = $organization->users()
            ->with('profile', 'contacts')
            ->paginate();

        return response()->json($users);
    }

    /**
     * Add a user to the organization
     *
     * @param Request $request
     * @param Organization $organization
     * @return JsonResponse
     */
    public function addUser(Request $request, Organization $organization): JsonResponse
    {
        $this->authorize('update', $organization);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:owner,admin,member'
        ]);

        $user = User::findOrFail($validated['user_id']);

        // Check if user is already in the organization
        if ($organization->users()->where('user_id', $user->id)->exists()) {
            throw ValidationException::withMessages([
                'user_id' => 'User is already a member of this organization'
            ]);
        }

        // Add user to organization with specified role
        $organization->users()->attach($user->id, ['role' => $validated['role']]);

        // Assign corresponding Spatie role
        $roleName = "{$organization->slug}-{$validated['role']}";
        $role = Role::firstOrCreate([
            'name' => $roleName,
            'guard_name' => 'web'
        ]);

        // Sync the user's role
        $user->assignRole($role);

        // Dispatch event to trigger role permissions sync
        event(new OrganizationMembershipChanged($organization, $user));

        return response()->json([
            'message' => 'User added to organization successfully',
            'data' => $organization->users()->where('user_id', $user->id)->first()
        ], 201);
    }

    /**
     * Remove a user from the organization
     *
     * @param Organization $organization
     * @param User $user
     * @return JsonResponse
     */
    public function removeUser(Organization $organization, User $user): JsonResponse
    {
        $this->authorize('update', $organization);

        // Check if the user is the sole owner
        $owners = $organization->users()->wherePivot('role', 'owner')->count();
        if ($owners <= 1 && $organization->users()->where('user_id', $user->id)->wherePivot('role', 'owner')->exists()) {
            return response()->json([
                'error' => 'Cannot remove the sole owner of the organization'
            ], 403);
        }

        // Get the role before detaching for cleanup
        $orgUserRole = $organization->users()->where('user_id', $user->id)->first()->pivot->role ?? null;

        // Remove user from organization
        $organization->users()->detach($user->id);

        // Remove organization-specific role if it exists
        if ($orgUserRole) {
            $roleName = "{$organization->slug}-{$orgUserRole}";
            $user->removeRole($roleName);
        }

        // Dispatch event to trigger role permissions sync
        event(new OrganizationMembershipChanged($organization, $user));

        return response()->json([
            'message' => 'User removed from organization successfully'
        ]);
    }

    /**
     * Update a user's role in the organization
     *
     * @param Request $request
     * @param Organization $organization
     * @param User $user
     * @return JsonResponse
     */
    public function updateUserRole(Request $request, Organization $organization, User $user): JsonResponse
    {
        $this->authorize('update', $organization);

        $validated = $request->validate([
            'role' => 'required|in:owner,admin,member'
        ]);

        // Check if user is in the organization
        if (!$organization->users()->where('user_id', $user->id)->exists()) {
            throw ValidationException::withMessages([
                'user_id' => 'User is not a member of this organization'
            ]);
        }

        // Check if the user is the sole owner and trying to change role
        $owners = $organization->users()->wherePivot('role', 'owner')->count();
        if (
            $owners <= 1 &&
            $organization->users()->where('user_id', $user->id)->wherePivot('role', 'owner')->exists() &&
            $validated['role'] !== 'owner'
        ) {
            return response()->json([
                'error' => 'Cannot change role of the sole owner of the organization'
            ], 403);
        }

        // Get old role for cleanup
        $oldRoleName = "{$organization->slug}-" . $organization->users()->where('user_id', $user->id)->first()->pivot->role;

        // Update user's organization role
        $organization->users()->updateExistingPivot($user->id, ['role' => $validated['role']]);

        // Update Spatie role assignment
        $newRoleName = "{$organization->slug}-{$validated['role']}";
        $newRole = Role::firstOrCreate([
            'name' => $newRoleName,
            'guard_name' => 'web'
        ]);

        // Remove old role and assign new role
        $user->removeRole($oldRoleName);
        $user->assignRole($newRole);

        // Dispatch event to trigger role permissions sync
        event(new OrganizationMembershipChanged($organization, $user));

        return response()->json([
            'message' => 'User role updated successfully',
            'data' => $organization->users()->where('user_id', $user->id)->first()
        ]);
    }

    /**
     * Get roles for an organization
     *
     * @param Organization $organization
     * @return JsonResponse
     */
    public function getRoles(Organization $organization): JsonResponse
    {
        $this->authorize('view', $organization);

        $roles = Role::where('name', 'like', "{$organization->slug}-%")->get();

        return response()->json(['data' => $roles]);
    }

    /**
     * Get permissions for an organization
     *
     * @param Organization $organization
     * @return JsonResponse
     */
    public function getPermissions(Organization $organization): JsonResponse
    {
        $this->authorize('view', $organization);

        $permissions = Permission::where('name', 'like', "org-{$organization->id}-%")->get();

        return response()->json(['data' => $permissions]);
    }
}
