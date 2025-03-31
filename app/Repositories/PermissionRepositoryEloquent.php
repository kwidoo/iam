<?php

namespace App\Repositories;

use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\Repositories\PermissionRepository;
use Spatie\Permission\Models\Permission;
use Kwidoo\Mere\Repositories\RepositoryEloquent;

/**
 * Class PermissionRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PermissionRepositoryEloquent extends RepositoryEloquent implements PermissionRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Permission::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
