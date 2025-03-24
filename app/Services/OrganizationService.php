<?php

namespace App\Services;

use App\Contracts\Repositories\OrganizationRepository;
use App\Contracts\Services\OrganizationService as OrganizationServiceContract;
use App\Models\Organization;
use Kwidoo\Mere\Contracts\MenuService;
use Kwidoo\Mere\Services\BaseService;

class OrganizationService extends BaseService implements OrganizationServiceContract
{
    protected Organization $organization;

    public function __construct(
        MenuService $menuService,
        OrganizationRepository $repository,
        string $slug = 'main'

    ) {
        parent::__construct($menuService, $repository);
        $this->organization = $repository->findByField('slug', $slug)->first();
    }

    protected function eventKey(): string
    {
        return 'organization';
    }
}
