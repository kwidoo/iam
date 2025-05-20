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
// Spatie\Permission\Models\Permission and Role might be removed if no longer directly used.
// Let's check after modifications if they are still needed.
use App\Contracts\Services\Organizations\OrganizationMembershipService;
use Kwidoo\Mere\Contracts\Services\MenuService; // Assuming ResourceController needs this
use Illuminate\Support\Facades\Log;
use Spatie\LaravelData\Data; // Added for joinOrganization

class OrganizationController extends ResourceController
{
    protected OrganizationMembershipService $organizationMembershipService;

    public function __construct(
        OrganizationMembershipService $organizationMembershipService,
        MenuService $menuService // Assuming parent needs this
        // If other dependencies are needed by parent ResourceController, they should be added here
    ) {
        // If ResourceController's constructor is parameterless, remove $menuService and this call.
        // If it needs more/different services, adjust accordingly.
        // This is based on the "safe approach" due to inability to inspect parent.
        parent::__construct($menuService); 
        $this->organizationMembershipService = $organizationMembershipService;
    }

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

        try {
            $result = $this->organizationMembershipService->addUserToOrganization($organization, $user, $validated['role']);
            return response()->json([
                'message' => 'User added to organization successfully',
                'data' => $result
            ], 201);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error adding user to organization: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to add user to organization.'], 500);
        }
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

        try {
            $this->organizationMembershipService->removeUserFromOrganization($organization, $user);
            return response()->json([
                'message' => 'User removed from organization successfully'
            ]);
        } catch (ValidationException $e) {
            // Re-throw to let Laravel's handler format it, or return a custom response
            // For consistency with how it might have been handled before:
            return response()->json(['errors' => $e->errors()], $e->status);
        } catch (\Exception $e) {
            Log::error('Error removing user from organization: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to remove user from organization.'], 500);
        }
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

        try {
            $result = $this->organizationMembershipService->updateUserRoleInOrganization($organization, $user, $validated['role']);
            return response()->json([
                'message' => 'User role updated successfully',
                'data' => $result
            ]);
        } catch (ValidationException $e) {
            // Re-throw to let Laravel's handler format it, or return a custom response
            // For consistency with how it might have been handled before:
             return response()->json(['errors' => $e->errors()], $e->status);
        } catch (\Exception $e) {
            Log::error('Error updating user role in organization: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to update user role.'], 500);
        }
    }

    /**
     * Get roles for an organization
     *
     * @param Organization $organization
     * @return JsonResponse
     */
    public function getRoles(Organization $organization): JsonResponse
    {
        $this->authorize('view', $organization); // Keep authorization
        $roles = $this->organizationMembershipService->getRolesForOrganization($organization);
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
        $this->authorize('view', $organization); // Keep authorization
        $permissions = $this->organizationMembershipService->getPermissionsForOrganization($organization);
        return response()->json(['data' => $permissions]);
    }

    public function joinOrganization(Request $request, Organization $organization): JsonResponse
    {
        $this->authorize('join', $organization); // Requires a 'join' policy on Organization model

        $user = Auth::user();
        if (!$user) {
            // Should be caught by auth:api middleware, but good practice
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        try {
            // Assuming $this->service is where DefaultOrganizationService (or its contract) is stored
            // by the parent ResourceController. This is a common pattern.
            // If not, this part would need adjustment based on how the actual service is injected/available.
            if (!property_exists($this, 'service') || !$this->service || !method_exists($this->service, 'connect')) {
                 Log::error('OrganizationService (DefaultOrganizationService) not available or does not have a connect method in OrganizationController.');
                 return response()->json(['error' => 'Service configuration error.'], 500);
            }
            
            $dataForService = Data::from([
                'slug' => $organization->slug,
                // user_id is not strictly needed by connect if it uses Auth::user() internally,
                // but passing it for explicitness if the service's DTO expects it.
                // The service was refactored to use Auth::user().
            ]);

            // The connect method in DefaultOrganizationService was refactored to use Auth::user()
            // and its OrganizationMembershipService to add the user.
            $this->service->connect($dataForService);

            return response()->json(['message' => 'Successfully joined organization.']);

        } catch (ValidationException $e) {
            throw $e; // Re-throw for Laravel's handler
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::warning("Authorization failed for joining organization {\$organization->slug}: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 403);
        } catch (\Exception $e) {
            Log::error("Error joining organization {\$organization->slug}: " . $e->getMessage(), [
                'organization_id' => $organization->id,
                'user_id' => $user->id,
                'exception' => $e
            ]);
            return response()->json(['error' => 'Failed to join organization.'], 500);
        }
    }
}
