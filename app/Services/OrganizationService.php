<?php

namespace App\Services;

use App\Contracts\Repositories\OrganizationRepository;
use App\Contracts\Repositories\UserRepository;
use App\Contracts\Services\OrganizationService as OrganizationServiceContract;
use App\Data\AccessAssignmentData;
use App\Data\RegistrationData;
use App\Models\Organization;
use App\Resolvers\AccessAssignmentStrategyResolver;
use Kwidoo\Mere\Contracts\Lifecycle;
use Kwidoo\Mere\Contracts\MenuService;
use Kwidoo\Mere\Services\BaseService;
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
        $this->lifecycle = $this->lifecycle->withoutAuth();

        $slug = $this->generateSlug();

        $this->organization = $this->create([
            'name' => "{$data->fname} {$data->lname} organization",
            'slug' => $slug,
            'owner_id' => $data->user->id,
            //   'role' => 'owner',
        ]);


        $this->attachToOrganization($data);
        $this->attachToProfile($data);

        $this->orgRoleInitializer->createDefaults($this->organization, $data, $this->lifecycle);

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
        $this->lifecycle = $this->lifecycle->withoutAuth();
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
        $this->lifecycle = $this->lifecycle->withoutAuth();

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
        $this->lifecycle = $this->lifecycle->withoutAuth();

        if ($data->organization) {
            return $data->organization;
        }

        $this->organization = $this->create([
            'name' => 'main',
            'slug' => 'main',
            'owner_id' => $data->user->id,
        ]);

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
        [$roleStrategy, $permissionStrategy] = $this->aasr->resolve($context, $this->lifecycle);
        $roleStrategy->assign($context->user, $this->organization);
        $permissionStrategy->assign($context->user, $this->organization);
    }
}
