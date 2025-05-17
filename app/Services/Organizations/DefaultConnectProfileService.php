<?php

namespace App\Services;

use App\Contracts\Services\Organizations\ConnectProfileService;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;
use Kwidoo\Mere\Contracts\Models\OrganizationInterface;
use Kwidoo\Mere\Contracts\Models\ProfileInterface;
use Kwidoo\Mere\Contracts\Repositories\OrganizationRepository;
use Kwidoo\Mere\Contracts\Services\MenuService;
use Kwidoo\Mere\Services\BaseService;

class DefaultConnectProfileService extends BaseService implements ConnectProfileService
{
    public function __construct(
        MenuService $menuService,
        OrganizationRepository $repository,
        Lifecycle $lifecycle,
    ) {
        parent::__construct($menuService, $repository, $lifecycle);
    }

    public function connect(ProfileInterface $profile, OrganizationInterface $organization): void
    {
        $this->repository->attachProfile($organization, $profile);
    }

    public function disconnect(ProfileInterface $profile, OrganizationInterface $organization): void
    {
        //$this->repository->detachProfile($organization, $profile);
    }

    protected function eventKey(): string
    {
        return 'organization_connect_profile';
    }
}
