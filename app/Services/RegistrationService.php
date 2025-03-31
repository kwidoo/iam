<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepository;
use App\Contracts\Services\RegistrationService as RegistrationServiceContract;
use App\Data\RegistrationData;
use App\Factories\OrganizationServiceFactory;
use App\Factories\ProfileServiceFactory;
use App\Factories\PasswordStrategyResolver;
use Kwidoo\Contacts\Contracts\ContactServiceFactory;
use Kwidoo\Mere\Contracts\Lifecycle;
use Kwidoo\Mere\Contracts\MenuService;

class RegistrationService extends UserService implements RegistrationServiceContract
{
    public function __construct(
        MenuService $menuService,
        UserRepository $repository,
        Lifecycle $lifecycle,
        protected ContactServiceFactory $csf,
        protected ProfileServiceFactory $psf,
        protected OrganizationServiceFactory $osf,
        protected PasswordStrategyResolver $strategy,
    ) {
        $this->lifecycle = $lifecycle->withoutAuth();
        parent::__construct($menuService, $repository, $this->lifecycle);
        $this->lifecycle = $this->lifecycle->withoutTrx();
    }

    protected function eventKey(): string
    {
        return 'registration';
    }

    public function registerNewUser(RegistrationData $data)
    {
        return $this->lifecycle
            ->run(
                'registerNewUser',
                $this->eventKey(),
                $data,
                fn() => $this->handleRegisterNewUser($data)
            );
    }

    protected function handleRegisterNewUser(RegistrationData $data)
    {
        $passwordStrategy = $this->strategy->resolve($data->otp ? 'otp' : 'password');
        $data->password = $passwordStrategy->password($data->otp);

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
}
