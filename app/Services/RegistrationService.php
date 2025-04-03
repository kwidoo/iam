<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepository;
use App\Contracts\Resolvers\OrganizationResolver;
use App\Contracts\Services\RegistrationService as RegistrationServiceContract;
use App\Data\RegistrationData;
use App\Factories\ConfigurationContext;
use App\Factories\OrganizationServiceFactory;
use App\Factories\ProfileServiceFactory;
use App\Resolvers\RegistrationStrategyResolver;
use App\Factories\WrappedContactServiceFactory;
use Illuminate\Database\Eloquent\Model;
use Kwidoo\Mere\Contracts\Lifecycle;
use Kwidoo\Mere\Contracts\MenuService;
use Kwidoo\Mere\Data\ListQueryData;
use Kwidoo\Mere\Data\ShowQueryData;
use App\Models\User;
use Exception;

class RegistrationService extends UserService implements RegistrationServiceContract
{
    public function __construct(
        MenuService $menuService,
        UserRepository $repository,
        protected Lifecycle $lifecycle,
        protected WrappedContactServiceFactory $csf,
        protected ProfileServiceFactory $psf,
        protected OrganizationServiceFactory $osf,
        protected RegistrationStrategyResolver $selector,
        protected ConfigurationContext $context,
        protected OrganizationResolver $resolver,
    ) {
        parent::__construct($menuService, $repository, $this->lifecycle);
    }

    /**
     * @return string
     */
    protected function eventKey(): string
    {
        return 'registration';
    }

    /**
     * @param RegistrationData $data
     *
     * @return User
     */
    public function registerNewUser(RegistrationData $data): User
    {
        $this->prepareRegistrationContext($data);

        return $this->lifecycle
            ->run(
                action: 'registerNewUser',
                resource: $this->eventKey(),
                context: $data,
                callback: fn() => $this->handleRegisterNewUser($data),
            );
    }

    protected function prepareRegistrationContext(RegistrationData $data): void
    {
        $data->organization = $this->resolver->resolve($data->orgName ?? null);

        $context = $this->context
            ->forOrg($data->organization)
            ->registrationConfig();

        $this->selector->setConfig($context);
    }

    /**
     * @param RegistrationData $data
     *
     * @return User
     */
    protected function handleRegisterNewUser(RegistrationData $data): User
    {
        $this->handleCreateUser($data);

        $this->handleIdentity($data);

        $this->handleProfile($data);

        $this->handleOrganization($data);

        return $data->user;
    }

    /**
     * @param RegistrationData $data
     *
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
            fn() => $this->handleCreate(['password' => $data->password])
        );
    }

    /**
     * @param RegistrationData $data
     *
     * @return void
     */
    protected function handleIdentity(RegistrationData $data): void
    {
        $contactService = $this->csf->make($data->user, $this->lifecycle->withoutTrx());
        $identity = $this->selector->resolve('identity', $contactService);
        $identity->create($data);
    }

    /**
     * @param RegistrationData $data
     *
     * @return void
     */
    protected function handleProfile(RegistrationData $data): void
    {
        $profileService = $this->psf->make($data->user, $this->lifecycle->withoutTrx());
        $strategy = $this->selector->resolve('profile', $profileService);
        $strategy->create($data);
    }

    /**
     * @param RegistrationData $data
     *
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



    /** DISABLED */
    public function list(ListQueryData $query)
    {
        throw new Exception('Not implemented');
    }

    public function getById(ShowQueryData $query): Model
    {
        throw new Exception('Not implemented');
    }

    public function update(string $id, array $data): mixed
    {
        throw new Exception('Not implemented');
    }

    public function delete(string $id): bool
    {
        throw new Exception('Not implemented');
    }
}
