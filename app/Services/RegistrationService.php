<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepository;
use App\Contracts\Services\RegistrationService as RegistrationServiceContract;
use App\Guards\OrganizationGuard;
use Kwidoo\Contacts\Contracts\ContactServiceFactory;
use Kwidoo\Mere\Contracts\MenuService;
use Kwidoo\Mere\Services\BaseService;

class RegistrationService extends BaseService implements RegistrationServiceContract
{
    protected ContactServiceFactory $csf;

    public function __construct(
        MenuService $menuService,
        UserRepository $repository,
        ContactServiceFactory $csf,
        protected OrganizationGuard $guard,
    ) {
        parent::__construct($menuService, $repository);
        $this->csf = $csf;
    }

    protected function eventKey(): string
    {
        return 'registration';
    }

    public function registerNewUser(array $data)
    {
        $user = $this->create(['password' => $data['password']]);
        $data['user_id'] = $user->id;
        $this->guard->checkCanRegister($data['organization'], $data);

        $contactService = $this->csf->make($user);
        $contactService->create(
            $data['method'],
            $data['value'],
        );

        $user->organizations()->attach($data['organization']);

        return $user;
    }
}
