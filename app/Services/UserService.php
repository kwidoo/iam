<?php

namespace App\Services;

use App\Contracts\Services\UserService as UserServiceContract;
use App\Contracts\Repositories\UserRepository;
use App\Presenters\UserPresenter;
use Kwidoo\Mere\Services\BaseService;
use Kwidoo\Mere\Contracts\MenuService;
use Kwidoo\Mere\Data\ShowQueryData;
use Illuminate\Database\Eloquent\Model;
use Kwidoo\Mere\Contracts\Lifecycle;

class UserService extends BaseService implements UserServiceContract
{
    public function __construct(
        MenuService $menuService,
        UserRepository $repository,
        Lifecycle $lifecycle,
    ) {
        parent::__construct($menuService, $repository, $lifecycle);
    }

    protected function eventKey(): string
    {
        return 'user';
    }
    /**
     * @param ShowQueryData $query
     *
     * @return Model
     */
    public function getById(ShowQueryData $query): Model
    {
        $query->presenter = UserPresenter::class;
        $query->with = ['roles', 'organizations.profiles'];

        return parent::getById($query);
    }
}
