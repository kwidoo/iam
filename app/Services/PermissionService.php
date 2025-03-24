<?php

namespace App\Services;

use App\Contracts\Services\PermissionService as PermissionServiceContract;
use App\Contracts\Repositories\PermissionRepository;
use Kwidoo\Mere\Services\BaseService;
use Kwidoo\Mere\Contracts\MenuService;

class PermissionService extends BaseService implements PermissionServiceContract
{
    public function __construct(MenuService $menuService, PermissionRepository $repository)
    {
        parent::__construct($menuService, $repository);
    }

    protected function eventKey(): string
    {
        return 'permission';
    }
}
