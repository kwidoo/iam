<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepository;
use App\Contracts\Services\RegistrationService as RegistrationServiceContract;
use App\Data\RegistrationData;
use App\Factories\OrganizationServiceFactory;
use App\Factories\ProfileServiceFactory;
use App\Factories\PasswordStrategyFactory;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Kwidoo\Contacts\Contracts\ContactServiceFactory;
use Kwidoo\Mere\Contracts\Lifecycle;
use Kwidoo\Mere\Contracts\MenuService;
use Kwidoo\Mere\Data\ListQueryData;
use Kwidoo\Mere\Data\ShowQueryData;

class RegistrationService extends UserService implements RegistrationServiceContract
{
    public function __construct(
        MenuService $menuService,
        UserRepository $repository,
        Lifecycle $lifecycle,
        protected ContactServiceFactory $csf,
        protected ProfileServiceFactory $psf,
        protected OrganizationServiceFactory $osf,
        protected PasswordStrategyFactory $strategy,
    ) {
        $this->lifecycle = $lifecycle->withoutAuth();
        parent::__construct($menuService, $repository, $this->lifecycle);
    }

    protected function eventKey(): string
    {
        return 'registration';
    }

    public function registerNewUser(RegistrationData $data)
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

        $passwordStrategy = $this->strategy->resolve($data->otp ? 'otp' : 'password');
        $data->password = $passwordStrategy->password($data->otp);

        /** @var User */
        $user = $this->create(['password' => $data->password]);

        $data->userId = $user->id;

        $contactService = $this->csf->make($user, $this->lifecycle);
        $contactService->create(
            $data->method,
            $data->value,
        );

        $profileService = $this->psf->make($user, $this->lifecycle);
        $profile = $profileService->registerProfile($data);

        if (!$data->organization) {
            $organizationService = $this->osf->make($user, $this->lifecycle);
            $data->organization = $organizationService->createDefaultForUser($data);
        }
        $user->organizations()->attach($data->organization);
        $profile->organizations()->attach($data->organization);

        return $user;
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
