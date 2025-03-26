<?php

namespace App\Repositories;

use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\Repositories\ProfileRepository;
use App\Models\Profile;
use Kwidoo\Mere\Repositories\RepositoryEloquent;

/**
 * Class ProfileRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ProfileRepositoryEloquent extends RepositoryEloquent implements ProfileRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Profile::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
