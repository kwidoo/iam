<?php

namespace App\Services;

use App\Contracts\Repositories\OrganizationRepository;
use App\Contracts\Repositories\UserRepository;
use App\Contracts\Services\OrganizationService as OrganizationServiceContract;
use App\Data\AccessAssignmentData;
use App\Data\RegistrationData;
use App\Models\Organization;
use App\Resolvers\AccessAssignmentStrategyResolver;
use App\Services\Base\BaseService;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;
use Kwidoo\Lifecycle\Data\LifecycleData;
use Kwidoo\Mere\Contracts\MenuService;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Kwidoo\Mere\Contracts\AccessAssignmentFactory;

class OrganizationService extends BaseService implements OrganizationServiceContract
{
    protected ?Organization $organization;

    public function __construct(
        MenuService $menuService,
        OrganizationRepository $repository,
        Lifecycle $lifecycle,
        protected ?string $slug = 'main',
        protected UserRepository $userRepository,
        protected AccessAssignmentFactory $factory,
        protected AccessAssignmentStrategyResolver $aasr,
        protected OrgRoleInitializerService $orgRoleInitializer,
    ) {
        parent::__construct($menuService, $repository, $lifecycle);
        $this->organization = $this->repository->findByField('slug', $slug)->first();
    }

    protected function eventKey(): string
    {
        return 'organization';
    }

    public function createDefaultForUser(RegistrationData $data): Organization
    {
        // Set options for this operation
        $authDisabledOptions = $this->options->withoutAuth();

        $slug = $this->generateSlug();

        // Create lifecycle data for creating organization
        $createLifecycleData = new LifecycleData(
            action: 'create',
            resource: $this->eventKey(),
            context: [
                'name' => "{$data->fname} {$data->lname} organization",
                'slug' => $slug,
                'owner_id' => $data->user->id,
            ]
        );

        // Create the organization with the new lifecycle
        $this->organization = $this->lifecycle->run(
            $createLifecycleData,
            function () use ($data, $slug) {
                return $this->handleCreate([
                    'name' => "{$data->fname} {$data->lname} organization",
                    'slug' => $slug,
                    'owner_id' => $data->user->id,
                ]);
            },
            $authDisabledOptions
        );

        $this->attachToOrganization($data);
        $this->attachToProfile($data);

        // Create lifecycle data for creating default roles
        $rolesLifecycleData = new LifecycleData(
            action: 'createDefaults',
            resource: $this->eventKey(),
            context: [
                'organization' => $this->organization,
                'data' => $data
            ]
        );

        // Initialize roles with the new lifecycle
        $this->lifecycle->run(
            $rolesLifecycleData,
            function () use ($data) {
                $this->orgRoleInitializer->createDefaults(
                    $this->organization,
                    $data,
                    $this->lifecycle
                );
            },
            $authDisabledOptions
        );

        $this->provideAccess($this->factory->resolve()->resolve($data));

        return $this->organization;
    }

    /**
     * @param RegistrationData $data
     *
     * @return Organization
     */
    public function loadDefault(RegistrationData $data): Organization
    {
        // Set options for this operation
        $authDisabledOptions = $this->options->withoutAuth();

        $this->organization = $this->repository->findByField('slug', 'main')->first();

        if ($this->organization) {
            $this->attachToOrganization($data);
            $this->attachToProfile($data);

            $this->provideAccess($this->factory->resolve()->resolve($data));

            return $this->organization;
        }

        return $this->createDefaultForUser($data);
    }

    public function connectToExistingOrg(RegistrationData $data): Organization
    {
        // Set options for this operation
        $authDisabledOptions = $this->options->withoutAuth();

        $this->organization = $data->organization;

        if (!$this->organization || !$this->organization->exists) {
            throw ValidationException::withMessages([
                'organization' => 'Invalid organization provided.',
            ]);
        }

        $this->attachToOrganization($data);
        $this->attachToProfile($data);

        $this->provideAccess($this->factory->resolve()->resolve($data));

        return $this->organization;
    }

    /**
     * @param RegistrationData $data
     *
     * @return Organization
     */
    public function createInitialOrganization(RegistrationData $data): Organization
    {
        // Set options for this operation
        $authDisabledOptions = $this->options->withoutAuth();

        if ($data->organization) {
            return $data->organization;
        }

        // Create lifecycle data for creating initial organization
        $createLifecycleData = new LifecycleData(
            action: 'create',
            resource: $this->eventKey(),
            context: [
                'name' => 'main',
                'slug' => 'main',
                'owner_id' => $data->user->id,
            ]
        );

        // Create the initial organization with the new lifecycle
        $this->organization = $this->lifecycle->run(
            $createLifecycleData,
            function () use ($data) {
                return $this->handleCreate([
                    'name' => 'main',
                    'slug' => 'main',
                    'owner_id' => $data->user->id,
                ]);
            },
            $authDisabledOptions
        );

        $this->attachToOrganization($data);
        $this->attachToProfile($data);

        return $this->organization;
    }

    protected function attachToOrganization(RegistrationData $data): void
    {
        if (!$data->user->organizations->contains($this->organization)) {
            $data->user->organizations()->attach($this->organization);
        }
    }

    protected function attachToProfile(RegistrationData $data): void
    {
        if (!$data->profile->organizations->contains($this->organization)) {
            $data->profile->organizations()->attach($this->organization);
        }
    }

    /**
     * @return string
     */
    protected function generateSlug(): string
    {
        do {
            $slug = config('iam.orgPrefix', 'org-') . md5(Str::lower(Str::random(20)));
        } while ($this->repository->findByField('slug', $slug)->isNotEmpty());
        return $slug;
    }

    protected function provideAccess(AccessAssignmentData $context): void
    {
        // Create lifecycle data for providing access
        $accessLifecycleData = new LifecycleData(
            action: 'provideAccess',
            resource: $this->eventKey(),
            context: $context
        );

        // Assign roles and permissions through the new lifecycle
        $this->lifecycle->run(
            $accessLifecycleData,
            function () use ($context) {
                [$roleStrategy, $permissionStrategy] = $this->aasr->resolve($context, $this->lifecycle);
                $roleStrategy->assign($context->user, $this->organization);
                $permissionStrategy->assign($context->user, $this->organization);
            },
            $this->options
        );
    }
}
