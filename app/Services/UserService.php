<?php

namespace App\Services;

use App\Contracts\Services\UserService as UserServiceContract;
use App\Contracts\Repositories\UserRepository;
use Kwidoo\Mere\Services\BaseService;
use Kwidoo\Mere\Contracts\MenuService;

class UserService extends BaseService implements UserServiceContract
{
    public function __construct(MenuService $menuService, UserRepository $repository)
    {
        parent::__construct($menuService, $repository);
    }

    protected function eventKey(): string
    {
        return 'user';
    }
}
