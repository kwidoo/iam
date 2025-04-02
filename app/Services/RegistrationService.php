<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepository;
use App\Contracts\Services\RegistrationService as RegistrationServiceContract;
use App\Data\RegistrationData;
use App\Factories\ConfigurationContext;
use App\Factories\OrganizationResolver;
use App\Factories\OrganizationServiceFactory;
use App\Factories\ProfileServiceFactory;
use App\Factories\StrategySelectorFactory;
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
        protected StrategySelectorFactory $selector,
        protected ConfigurationContext $config,
        protected OrganizationResolver $orgResolver,
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
                callback: $this->resolveCallback($data)
            );
    }

    protected function prepareRegistrationContext(RegistrationData $data): void
    {
        $data->organization = $this->orgResolver->resolve($data->orgName ?? null);

        $config = $this->config
            ->forOrg($data->organization)
            ->registrationConfig();

        $this->selector->setConfig($config);

        $existingUser = $this->repository->whereHas('contacts', function ($query) use ($data) {
            $query->where('type', $data->method)
                ->where('value', $data->value);
        })->first();

        $data->user = $existingUser;
        $data->profile = $existingUser?->profile;
    }

    protected function resolveCallback(RegistrationData $data): callable
    {
        return $data->user
            ? fn() => $this->handleReuse($data)
            : fn() => $this->handleRegisterNewUser($data);
    }

    protected function handleReuse(RegistrationData $data): User
    {
        $this->handleOrganization($data);

        $this->attachToOrganization($data);
        $this->attachToProfile($data);

        return $data->user;
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

        $this->attachToOrganization($data);
        $this->attachToProfile($data);

        return $data->user;
    }

    /**
     * @param RegistrationData $data
     *
     * @return void
     */
    protected function handleCreateUser(RegistrationData $data): void
    {
        /** @var \App\Contracts\Services\Strategy<\App\Strategies\WithOTP|\App\Strategies\WithPassword> */
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
        /** @var \App\Contracts\Services\Strategy<\App\Strategies\IdentityStrategy> $identity */
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
        /** @var \App\Contracts\Services\Strategy<\App\Strategies\ProfileStrategy> $strategy */
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
        if ($data->organization) {
            return;
        }

        $organizationService = $this->osf->make($data->user, $this->lifecycle->withoutTrx());

        /** @var \App\Contracts\Services\Strategy<\App\Strategies\OrganizationStrategy> $strategy */
        $strategy = $this->selector->resolve('flow', $organizationService);
        $strategy->create($data);
    }

    protected function attachToOrganization(RegistrationData $data): void
    {
        if (!$data->user->organizations->contains($data->organization)) {
            $data->user->organizations()->attach($data->organization);
        }
    }

    protected function attachToProfile(RegistrationData $data): void
    {
        if (!$data->profile->organizations->contains($data->organization)) {
            $data->profile->organizations()->attach($data->organization);
        }
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
