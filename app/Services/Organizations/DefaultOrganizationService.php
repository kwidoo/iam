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
    protected function handleConnect(OrganizationCreateData $data): OrganizationInterface
    {
        $this->organization = $this->repository->findByField('slug', $data->slug)->first();

        if (!$this->organization || !$this->organization->exists) {
            throw ValidationException::withMessages([
                'organization' => 'Invalid organization provided.',
            ]);
        }

        // <- provide default access to use

        return $this->organization;
    }

    /**
     * @param \App\Data\Organizations\OrganizationCreateData $data
     *
     * @return \App\Models\Organization
     * lifecycle is called in create method of base service
     */
    protected function handleCreate(BaseData $data): OrganizationInterface
    {
        $this->organization = $this->repository->create([
            'name' => $data->name,
            'slug' => $data->slug,
            'owner_id' => $data->ownerId,
        ]);

        // <- provide admin access to creator
        return $this->organization;
    }

    protected function eventKey(): string
    {
        return 'organization';
    }
}
