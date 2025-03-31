<?php

namespace App\Repositories;

use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\Repositories\RoleRepository;
use Spatie\Permission\Models\Role;
use Kwidoo\Mere\Repositories\RepositoryEloquent;

/**
 * Class RoleRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class RoleRepositoryEloquent extends RepositoryEloquent implements RoleRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Role::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
