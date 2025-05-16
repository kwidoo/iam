<?php

namespace App\Services;

use App\Contracts\Services\Organizations\OrganizationService;
use App\Contracts\Services\ProfileService;
use App\Contracts\Services\Registration\RegistrationService;
use App\Data\Profile\ProfileCreateData;
use App\Factories\ContactServiceFactory;
use App\Services\BaseService;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;
use App\Models\User;
use App\Resolvers\Registration\RegistrationConfigResolver;
use App\Resolvers\Registration\RegistrationStrategyResolver;
use Kwidoo\Mere\Services\Traits\OnlyCreate;
use Kwidoo\Lifecycle\Traits\RunsLifecycle;
use Kwidoo\Lifecycle\Data\LifecycleResultData;
use Kwidoo\Mere\Contracts\Models\UserInterface;
use Kwidoo\Mere\Contracts\Repositories\UserRepository;
use Kwidoo\Mere\Contracts\Services\MenuService;
use Spatie\LaravelData\Contracts\BaseData;

/**
 * Service responsible for handling user registration process.
 * Implements a strategy-based approach for different registration flows.
 */
class DefaultRegistrationService extends BaseService implements RegistrationService
{
    use OnlyCreate;
    use RunsLifecycle;

    /**
     * Initialize the registration service with required dependencies.
     */
    public function __construct(
        MenuService $menuService,
        UserRepository $repository,
        Lifecycle $lifecycle,
        protected ContactServiceFactory $csf,
        protected ProfileService $profileService,
        protected OrganizationService $organizationService,
        protected RegistrationStrategyResolver $selector,
        protected RegistrationConfigResolver $context
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
     * @param  \App\Data\Registration\DefaultRegistrationData $data
     * @return UserInterface
     */
    public function registerNewUser(BaseData $data): User
    {
        $this->prepareRegistrationContext($data);

        return $this->runLifecycle(
            context: $data,
            callback: fn() => $this->handleRegisterNewUser($data),
        );
    }

    /**
     * Prepare the registration context by setting up organization
     * and configuration for the registration process.
     *
     * @param  \App\Data\Registration\DefaultRegistrationData $data
     * @return void
     */
    protected function prepareRegistrationContext(BaseData $data): void
    {
        $context = $this->context
            ->forOrganization($data->orgName ?? null)
            ->registrationConfig();

        $data->organization = $this->context->getOrganization();

        $this->selector->setConfig($context);
    }

    /**
     * Handle the complete user registration process.
     * Creates user, sets up identity, profile and organization associations.
     *
     * @param  \App\Data\Registration\DefaultRegistrationData $data
     * @return User
     */
    protected function handleRegisterNewUser(BaseData $data): User
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
     * @param  \App\Data\Registration\DefaultRegistrationData $data
     * @return void
     */
    protected function handleCreateUser(BaseData $data): void
    {
        $strategy = $this->selector->resolve('secret', NullService::class);
        $strategy->create($data);

        $this->runLifecycle(
            context: $data,
            callback: fn() => $this->handleCreate($data),
        );
    }

    /**
     * Handle user identity setup (email or phone) based on registration data.
     *
     * @param \App\Data\Registration\DefaultRegistrationData $data
     * @return void
     */
    protected function handleIdentity(BaseData $data): void
    {
        $contactService = $this->csf->make($data->user, $this->lifecycle);
        $identity = $this->selector->resolve('identity', $contactService);
        $identity->create($data);
    }

    /**
     * Handle user profile creation based on registration data.
     *
     * @param  \App\Data\Registration\DefaultRegistrationData $data
     * @return void
     */
    protected function handleProfile(BaseData $data): void
    {
        $this->profileService->create(ProfileCreateData::from([
            'fname' => $data->fname,
            'lname' => $data->lname,
            'user_id' => $data->user->id,
            'dob' => $data->dob,
            'gender' => $data->gender
        ]));
    }

    /**
     * Handle organization association for the new user.
     * Uses the appropriate flow strategy based on registration context.
     *
     * @param \App\Data\Registration\DefaultRegistrationData $data
     * @return void
     */
    protected function handleOrganization(BaseData $data): void
    {
        $strategy = $this->selector->resolve('flow', $this->organizationService);
        $strategy->create($data);
    }

    /**
     * Handle the creation of a new resource.
     *
     * @param \App\Data\Registration\DefaultRegistrationData $data The data for creating the new resource
     * @return LifecycleResultData The newly created resource
     */
    protected function handleCreate(BaseData $data): mixed
    {
        $data->user = $this->repository->create([
            'password' => $data->password
        ]);

        return $data->user;
    }
}
