<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepository;
use App\Contracts\Services\OrganizationService;
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
        $this->guard->checkCanRegister($data['organization'], $data);
        $model = $this->create($data['password']);
        $contactService = $this->csf->make($model);
        $contactService->create(
            $data['value'],
            $data['type'],
        );

        return $model;
    }
}
