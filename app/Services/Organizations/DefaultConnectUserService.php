<?php

namespace App\Services\Organizations;

use App\Contracts\Services\Organizations\ConnectUserService;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;
use Kwidoo\Mere\Contracts\Models\OrganizationInterface;
use Kwidoo\Mere\Contracts\Models\UserInterface;
use Kwidoo\Mere\Contracts\Repositories\OrganizationRepository;
use Kwidoo\Mere\Contracts\Services\MenuService;
use Kwidoo\Mere\Services\BaseService;

class DefaultConnectUserServiceService extends BaseService implements ConnectUserService
{
    public function __construct(
        MenuService $menuService,
        OrganizationRepository $repository,
        Lifecycle $lifecycle
    ) {
        parent::__construct($menuService, $repository, $lifecycle);
    }

    public function connect(UserInterface $user, OrganizationInterface $organization): void
    {
        $this->repository->attachUser($organization, $user);
    }

    public function disconnect(UserInterface $user, OrganizationInterface $organization): void
    {
        // $this->repository->attachUser($organization, $user);

    }

    protected function eventKey(): string
    {
        return 'organization_connect_user';
    }
}
