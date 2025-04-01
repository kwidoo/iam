<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepository;
use App\Contracts\Services\RegistrationService as RegistrationServiceContract;
use App\Data\RegistrationData;
use App\Factories\ConfigurationContext;
use App\Factories\OrganizationServiceFactory;
use App\Factories\ProfileServiceFactory;
use App\Factories\StrategySelectorFactory;
use Illuminate\Database\Eloquent\Model;
use Kwidoo\Contacts\Contracts\ContactServiceFactory;
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
        Lifecycle $lifecycle,
        protected ContactServiceFactory $csf,
        protected ProfileServiceFactory $psf,
        protected OrganizationServiceFactory $osf,
        protected StrategySelectorFactory $selector,
        protected ConfigurationContext $config,

    ) {
        $this->lifecycle = $lifecycle->withoutAuth();
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
        return $this->lifecycle
            ->run(
                action: 'registerNewUser',
                resource: $this->eventKey(),
                context: $data,
                callback: fn() => $this->handleRegisterNewUser($data)
            );
    }

    /**
     * @param RegistrationData $data
     *
     * @return User
     */
    protected function handleRegisterNewUser(RegistrationData $data): User
    {
        $this->lifecycle = $this->lifecycle->withoutTrx();

        $this->handleCreateUser($data);

        $this->handleIdentity($data);

        $this->handleProfile($data);

        $this->handleOrganization($data);

        $data->user->organizations()->attach($data->organization);
        $data->profile->organizations()->attach($data->organization);

        return $data->user;
    }

    /**
     * @param RegistrationData $data
     *
     * @return null
     */
    protected function handleCreateUser(RegistrationData $data)
    {
        /** @var \App\Contracts\Services\Strategy<\App\Strategies\WithOTP|\App\Strategies\WithPassword> */
        $strategy = $this->selector->resolve($data->otp ? 'otp' : 'password', NullService::class);
        $strategy->create($data);

        $data->user = $this->create(['password' => $data->password]);
    }

    /**
     * @param RegistrationData $data
     *
     * @return null
     */
    protected function handleIdentity(RegistrationData $data)
    {
        $contactService = $this->csf->make($data->user, $this->lifecycle);
        /** @var \App\Contracts\Services\Strategy<\App\Strategies\IdentityStrategy> $identity */
        $identity = $this->selector->resolve($data->method, $contactService);
        $identity->create($data);
    }

    /**
     * @param RegistrationData $data
     *
     * @return null
     */
    protected function handleProfile(RegistrationData $data)
    {
        $profileService = $this->psf->make($data->user, $this->lifecycle);
        /** @var \App\Contracts\Services\Strategy<\App\Strategies\ProfileStrategy> $strategy */
        $strategy = $this->selector->resolve('default_profile', $profileService);
        $data = $strategy->create($data);
    }

    /**
     * @param RegistrationData $data
     *
     * @return null
     */
    protected function handleOrganization(RegistrationData $data)
    {
        if ($data->organization) {
            return;
        }

        $organizationService = $this->osf->make($data->user, $this->lifecycle);
        $strategyKey = $this->config->forOrg(null)->strategy();

        /** @var \App\Contracts\Services\Strategy<\App\Strategies\OrganizationStrategy> $strategy */
        $strategy = $this->selector->resolve($strategyKey, $organizationService);
        $data = $strategy->create($data);
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
