<?php

namespace App\Services;

use App\Contracts\Services\UserService as UserServiceContract;
use App\Contracts\Repositories\UserRepository;
use App\Presenters\UserPresenter;
use App\Services\Base\BaseService;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;
use Kwidoo\Mere\Contracts\MenuService;
use Kwidoo\Mere\Data\ShowQueryData;
use Illuminate\Database\Eloquent\Model;
use Kwidoo\Mere\Data\ListQueryData;

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
     * @param ListQueryData $query
     *
     * @return mixed
     */
    public function list(ListQueryData $query)
    {
        $query->presenter = UserPresenter::class;
        $query->with = ['roles', 'organizations.profiles'];

        return parent::list($query);
    }

    /**
     * @param ShowQueryData $query
     *
     * @return Model|array
     */
    public function getById(ShowQueryData $query): Model|array
    {
        $query->presenter = UserPresenter::class;
        $query->with = ['roles', 'organizations.profiles'];

        return parent::getById($query);
    }
}
