<?php

namespace App\Services;

use App\Contracts\Services\RoleService as RoleServiceContract;
use App\Contracts\Repositories\RoleRepository;
use Kwidoo\Mere\Contracts\Lifecycle;
use Kwidoo\Mere\Services\BaseService;
use Kwidoo\Mere\Contracts\MenuService;

class RoleService extends BaseService implements RoleServiceContract
{
    public function __construct(
        MenuService $menuService,
        RoleRepository $repository,
        Lifecycle $lifecycle,
    ) {
        parent::__construct($menuService, $repository, $lifecycle);
    }

    protected function eventKey(): string
    {
        return 'role';
    }
}
