<?php

namespace App\Services;

use App\Contracts\Services\ProfileService as ProfileServiceContract;
use App\Contracts\Repositories\ProfileRepository;
use App\Models\User;
use Kwidoo\Mere\Services\BaseService;
use Kwidoo\Mere\Contracts\MenuService;

class ProfileService extends BaseService implements ProfileServiceContract
{
    public function __construct(MenuService $menuService, ProfileRepository $repository, protected User $user)
    {
        parent::__construct($menuService, $repository);
    }

    protected function eventKey(): string
    {
        return 'profile';
    }
}
