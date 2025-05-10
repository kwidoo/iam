<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepository;
use App\Contracts\Services\RegistrationService as RegistrationServiceContract;
use App\Data\RegistrationData;
use App\Resolvers\ConfigurationContextResolver;
use App\Factories\OrganizationServiceFactory;
use App\Factories\ProfileServiceFactory;
use App\Resolvers\RegistrationStrategyResolver;
use App\Factories\ContactServiceFactory;
use App\Services\Base\BaseService;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;
use Kwidoo\Lifecycle\Data\LifecycleData;
use Kwidoo\Lifecycle\Data\LifecycleOptionsData;
use Kwidoo\Mere\Contracts\MenuService;
use App\Models\User;
use App\Services\Traits\OnlyCreate;

/**
 * Service responsible for handling user registration process.
 * Implements a strategy-based approach for different registration flows.
 */
class RegistrationService extends BaseService implements RegistrationServiceContract
{
    use OnlyCreate;

    /**
     * Initialize the registration service with required dependencies.
     *
     * @param MenuService                  $menuService
     * @param UserRepository               $repository
     * @param Lifecycle                    $lifecycle
     * @param ContactServiceFactory        $csf
     * @param ProfileServiceFactory        $psf
     * @param OrganizationServiceFactory   $osf
     * @param RegistrationStrategyResolver $selector
     * @param ConfigurationContextResolver $context
     */
    public function __construct(
        MenuService $menuService,
        UserRepository $repository,
        Lifecycle $lifecycle,
        protected ContactServiceFactory $csf,
        protected ProfileServiceFactory $psf,
        protected OrganizationServiceFactory $osf,
        protected RegistrationStrategyResolver $selector,
        protected ConfigurationContextResolver $context,
    ) {
        parent::__construct($menuService, $repository, $lifecycle);
    }

    /**
     * Get the event key for registration lifecycle events.
     *
     * @return string
     */
    protected function eventKey(): string
    {
        return 'registration';
    }

    /**
     * Register a new user with the provided registration data.
     * Handles the complete registration flow including user creation,
     * identity verification, profile setup, and organization association.
     *
     * @param  RegistrationData $data
     * @return User
     */
    public function registerNewUser(RegistrationData $data): User
    {
        $this->prepareRegistrationContext($data);

        $resource = $data->organization?->registration_mode->isInviteOnly()
            ? $this->eventKey() . '-invite'
            : $this->eventKey();

        $lifecycleData = new LifecycleData(
            action: 'registerNewUser',
            resource: $resource,
            context: $data
        );

        return $this->lifecycle->run(
            $lifecycleData,
            function () use ($data) {
                return $this->handleRegisterNewUser($data);
            },
            $this->options
        );
    }

    /**
     * Prepare the registration context by setting up organization
     * and configuration for the registration process.
     *
     * @param  RegistrationData $data
     * @return void
     */
    protected function prepareRegistrationContext(RegistrationData $data): void
    {
        $context = $this->context
            ->forOrg($data->orgName ?? null)
            ->registrationConfig();

        $data->organization = $this->context->getOrg();

        $this->selector->setConfig($context);
    }

    /**
     * Handle the complete user registration process.
     * Creates user, sets up identity, profile and organization associations.
     *
     * @param  RegistrationData $data
     * @return User
     */
    protected function handleRegisterNewUser(RegistrationData $data): User
    {
        $this->handleCreateUser($data);

        $this->handleProfile($data);

        $this->handleOrganization($data);

        $this->handleIdentity($data);

        return $data->user;
    }

    /**
     * Create a new user based on registration data.
     * Uses the appropriate secret strategy (OTP or password).
     *
     * @param  RegistrationData $data
     * @return void
     */
    protected function handleCreateUser(RegistrationData $data): void
    {
        $strategy = $this->selector->resolve('secret', NullService::class);
        $strategy->create($data);

        $lifecycleData = new LifecycleData(
            action: 'create',
            resource: $this->eventKey(),
            context: $data
        );

        $noTrxNoAuthOptions = $this->options
            ->withoutTrx()
            ->withoutAuth();

        $data->user = $this->lifecycle->run(
            $lifecycleData,
            fn() => $this->handleCreate($data->toArray()),
            $noTrxNoAuthOptions
        );
    }

    /**
     * Handle user identity setup (email or phone) based on registration data.
     *
     * @param  RegistrationData $data
     * @return void
     */
    protected function handleIdentity(RegistrationData $data): void
    {
        $contactService = $this->csf->make($data->user, $this->lifecycle);
        $identity = $this->selector->resolve('identity', $contactService);
        $identity->create($data);
    }

    /**
     * Handle user profile creation based on registration data.
     *
     * @param  RegistrationData $data
     * @return void
     */
    protected function handleProfile(RegistrationData $data): void
    {
        $profileService = $this->psf->make($data->user, $this->lifecycle);
        $strategy = $this->selector->resolve('profile', $profileService);
        $strategy->create($data);
    }

    /**
     * Handle organization association for the new user.
     * Uses the appropriate flow strategy based on registration context.
     *
     * @param  RegistrationData $data
     * @return void
     */
    protected function handleOrganization(RegistrationData $data): void
    {
        $context = $this->context
            ->forOrg($data->organization)
            ->forUser($data->user)
            ->registrationConfig();

        $this->selector->setConfig($context);

        $organizationService = $this->osf->make($this->lifecycle);
        $strategy = $this->selector->resolve('flow', $organizationService);
        $strategy->create($data);
    }

    /**
     * Handle the creation of a new resource.
     *
     * @param array $data The data for creating the new resource
     * @return mixed The newly created resource
     */
    protected function handleCreate(array $data): mixed
    {
        return $this->repository->create($data);
    }
}
