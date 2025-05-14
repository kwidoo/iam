<?php

namespace App\Services\Organizations;

use Kwidoo\Mere\Contracts\Repositories\OrganizationRepository;
use App\Contracts\Services\Organizations\OrganizationService;
use App\Contracts\Services\Organizations\RoleSetupService;
use App\Data\Organizations\OrganizationCreateData;
use Kwidoo\Mere\Contracts\Data\RegistrationData;
use Kwidoo\Mere\Contracts\Models\OrganizationInterface;
use App\Resolvers\AccessAssignmentStrategyResolver;
use App\Services\BaseService;
use Kwidoo\Lifecycle\Traits\RunsLifecycle;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Kwidoo\Mere\Contracts\AccessAssignmentFactory;
use Kwidoo\Mere\Contracts\Data\AccessAssignmentData;
use Kwidoo\Mere\Contracts\Repositories\UserRepository;
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
        protected ?string $slug = 'main',
        protected UserRepository $userRepository,
        protected AccessAssignmentFactory $factory,
        protected AccessAssignmentStrategyResolver $aasr,
        protected RoleSetupService $orgRoleInitializer,
    ) {
        parent::__construct($menuService, $repository, $lifecycle);

        $this->organization = $this->repository->findByField('slug', $slug)->first();
    }


    /**
     * @param \App\Data\Registration\DefaultRegistrationData $data
     *
     * @return \App\Models\Organization
     */
    public function loadDefault(RegistrationData $data): OrganizationInterface
    {
        return $this->runLifecycle(
            context: $data,
            callback: function () use ($data) {
                $this->organization = $this->repository->findByField('slug', 'main')->first();
                return $this->organization ? $this->finalize($data) : $this->handleCreate($data);
            }
        );
    }

    /**
     * @param \App\Data\Registration\DefaultRegistrationData $data
     *
     * @return \App\Models\Organization
     */
    protected function finalize(RegistrationData $data): OrganizationInterface
    {
        return $this->runLifecycle(
            context: $data,
            callback: function () use ($data) {
                $this->attachToOrganization($data);
                $this->attachToProfile($data);

                $this->provideAccess($this->factory->resolve()->resolve($data));

                return $data->organization;
            }
        );
    }

    /**
     * @param \App\Data\Registration\DefaultRegistrationData $data
     *
     * @return \App\Models\Organization
     */
    public function connectToExistingOrg(RegistrationData $data): OrganizationInterface
    {
        return $this->runLifecycle(
            context: $data,
            callback: function () use ($data) {
                $this->organization = $data->organization;

                if (!$this->organization || !$this->organization->exists) {
                    throw ValidationException::withMessages([
                        'organization' => 'Invalid organization provided.',
                    ]);
                }

                return $this->finalize($data);
            }
        );
    }

    /**
     * @param \App\Data\Registration\DefaultRegistrationData $data
     *
     * @return \App\Models\Organization
     */
    protected function handleCreate(BaseData $data): OrganizationInterface
    {
        return $this->runLifecycle(
            context: $data,
            callback: function () use ($data) {
                $organizationData = OrganizationCreateData::from(
                    name: "{$data->fname} {$data->lname} organization",
                    slug: $this->generateSlug(),
                    ownerId: $data->user->id,
                );

                $this->organization = parent::handleCreate($organizationData);

                $this->attachToOrganization($data);
                $this->attachToProfile($data);

                $this->orgRoleInitializer->initialize(
                    $this->organization,
                    $data,
                );

                $this->provideAccess($this->factory->resolve()->resolve($data));

                return $this->organization;
            }
        );
    }


    /**
     * @param \App\Data\Registration\DefaultRegistrationData $data
     *
     * @return void
     */
    protected function attachToOrganization(RegistrationData $data): void
    {
        $this->runLifecycle(
            context: ['user' => $data->user, 'organization' => $this->organization],
            callback: function () use ($data) {
                if (!$data->user->organizations->contains($this->organization)) {
                    // This is an allowed exception to the repository pattern
                    // as per the instructions since it's a relationship operation
                    $data->user->organizations()->attach($this->organization);
                }
            }
        );
    }

    /**
     * @param \App\Data\Registration\DefaultRegistrationData $data
     *
     * @return void
     */
    protected function attachToProfile(RegistrationData $data): void
    {
        $this->runLifecycle(
            context: ['profile' => $data->profile, 'organization' => $this->organization],
            callback: function () use ($data) {
                if (!$data->profile->organizations->contains($this->organization)) {
                    // This is an allowed exception to the repository pattern
                    // as per the instructions since it's a relationship operation
                    $data->profile->organizations()->attach($this->organization);
                }
            }
        );
    }

    /**
     * @return string
     */
    protected function generateSlug(): string
    {
        return $this->runLifecycle(
            context: ['prefix' => config('iam.orgPrefix', 'org:')],
            callback: function () {
                do {
                    $slug = config('iam.orgPrefix', 'org:') . md5(Str::lower(Str::random(20)));
                } while ($this->repository->findByField('slug', $slug)->isNotEmpty());
                return $slug;
            }
        );
    }

    /**
     * @param \App\Data\DefaultAccessAssignmentData;
     *
     * @return void
     */
    protected function provideAccess(AccessAssignmentData $context): void
    {
        $this->runLifecycle(
            context: $context,
            callback: function () use ($context) {
                [$roleStrategy, $permissionStrategy] = $this->aasr->resolve($context, $this->lifecycle);
                $roleStrategy->assign($context->user, $this->organization);
                $permissionStrategy->assign($context->user, $this->organization);
            }
        );
    }

    protected function eventKey(): string
    {
        return 'organization';
    }
}
