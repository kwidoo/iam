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
use Kwidoo\Mere\Contracts\Lifecycle;
use Kwidoo\Mere\Contracts\MenuService;
use App\Models\User;
use App\Services\Traits\OnlyCreate;

/**
 * Service responsible for handling user registration process.
 * Implements a strategy-based approach for different registration flows.
 */
class RegistrationService extends UserService implements RegistrationServiceContract
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
        protected Lifecycle $lifecycle,
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
            ? $this->eventKey($data) . '-invite'
            : $this->eventKey();

        return $this->lifecycle
            ->run(
                action: 'registerNewUser',
                resource: $resource,
                context: $data,
                callback: fn() => $this->handleRegisterNewUser($data),
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

        $data->user = $this->lifecycle->withoutTrx()->withoutAuth()->run(
            'create',
            $this->eventKey(),
            $data,
            fn() => $this->handleCreate($data->toArray()),
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
        $contactService = $this->csf->make($data->user, $this->lifecycle->withoutTrx());
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
        $profileService = $this->psf->make($data->user, $this->lifecycle->withoutTrx());
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

        $organizationService = $this->osf->make($this->lifecycle->withoutTrx());
        $strategy = $this->selector->resolve('flow', $organizationService);
        $strategy->create($data);
    }
}
