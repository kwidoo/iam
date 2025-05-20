<?php

namespace App\Services\Organizations;

use Kwidoo\Mere\Contracts\Repositories\OrganizationRepository;
use App\Contracts\Services\Organizations\OrganizationService;
use App\Contracts\Services\Organizations\RoleSetupService;
use App\Data\Organizations\OrganizationCreateData;
use Kwidoo\Mere\Contracts\Models\OrganizationInterface;
use App\Services\BaseService;
use Kwidoo\Lifecycle\Traits\RunsLifecycle;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;
use Illuminate\Validation\ValidationException;
use Kwidoo\Mere\Contracts\Services\MenuService;
use Spatie\LaravelData\Contracts\BaseData;
use App\Contracts\Services\Organizations\OrganizationMembershipService; // Added
use Illuminate\Support\Facades\Auth; // Added

/**
 * @property \App\Models\Organization $organization
 */
class DefaultOrganizationService extends BaseService implements OrganizationService
{
    use RunsLifecycle;

    /** @var \App\Models\Organization|null */
    protected ?OrganizationInterface $organization;

    public function __construct(
        MenuService $menuService,
        OrganizationRepository $repository,
        Lifecycle $lifecycle,
        protected RoleSetupService $orgRoleInitializer,
        protected OrganizationMembershipService $organizationMembershipService, // Added
        protected ?string $slug = 'main',
    ) {
        parent::__construct($menuService, $repository, $lifecycle);

        $this->organization = $this->findBySlug('slug', $slug);
    }

    /**
     * @param \App\Data\Organizations\OrganizationCreateData $data
     *
     * @return \App\Models\Organization
     */
    public function connect(BaseData $data): OrganizationInterface
    {
        // <-add authorizer, that checks:
        // - if user is allowed to connect to this organization
        // - if user is not owner of organization
        // - if user is not already connected to this organization
        return $this->runLifecycle(
            context: $data,
            callback: fn() => $this->handleConnect($data)
        );
    }

    public function findBySlug(string $slug): ?OrganizationInterface
    {
        return $this->repository->findByField('slug', $slug)->first();
    }

    /**
     * Connect a user to an existing organization.
     *
     * @param \App\Data\Organizations\OrganizationCreateData $data
     *
     * @return \App\Models\Organization
     */
    protected function handleConnect(BaseData $data): OrganizationInterface
    {
        if (!isset($data->slug)) {
            throw new \InvalidArgumentException('Organization slug is required to connect.');
        }

        $organization = $this->repository->findByField('slug', $data->slug)->first();

        if (!$organization || !$organization->exists) {
            throw ValidationException::withMessages([
                'organization' => 'Invalid organization provided.',
            ]);
        }

        $user = Auth::user();
        if (!$user) {
            throw ValidationException::withMessages([
                'user' => 'User must be authenticated to join an organization.',
            ]);
        }

        // Check if user is already a member
        if ($organization->users()->where('user_id', $user->id)->exists()) {
            throw ValidationException::withMessages([
                'user_id' => 'User is already a member of this organization.'
            ]);
        }
        
        // Add user to organization with a default role 'member'
        // This uses the OrganizationMembershipService to ensure all related logic (Spatie roles, events) is handled.
        $this->organizationMembershipService->addUserToOrganization($organization, $user, 'member');

        // The connect method in this service returns the organization model.
        // The addUserToOrganization returns the pivot model, so we just return the organization.
        return $organization;
    }

    /**
     * @param \App\Data\Organizations\OrganizationCreateData $data
     *
     * @return \App\Models\Organization
     * lifecycle is called in create method of base service
     */
    protected function handleCreate(BaseData $data): OrganizationInterface
    {
        // Ensure ownerId is present, as it's used by the original logic
        if (!isset($data->ownerId)) {
            // Attempt to get from auth user if not provided, or throw error
            $data->ownerId = Auth::id() ?? throw new \InvalidArgumentException('Owner ID is required to create an organization.');
        }
        if (!isset($data->name) || !isset($data->slug)) {
             throw new \InvalidArgumentException('Name and Slug are required to create an organization.');
        }

        $this->organization = $this->repository->create([
            'name' => $data->name,
            'slug' => $data->slug,
            'owner_id' => $data->ownerId,
        ]);

        // <- provide admin access to creator - This part is still a comment from original code
        // If the creator should be added as 'owner' via OrganizationMembershipService:
        // $owner = \App\Models\User::find($data->ownerId);
        // if ($owner) {
        //     $this->organizationMembershipService->addUserToOrganization($this->organization, $owner, 'owner');
        // }

        return $this->organization;
    }

    protected function eventKey(): string
    {
        return 'organization';
    }
}
