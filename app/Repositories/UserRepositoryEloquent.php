<?php

namespace App\Repositories;

use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\Repositories\UserRepository;
use App\Models\User;
use Kwidoo\Mere\Repositories\RepositoryEloquent;

/**
 * Class UserRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserRepositoryEloquent extends RepositoryEloquent implements UserRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @param array $attributes
     *
     * @return User
     * @todo find out why it is so
     */
    public function create(array $attributes)
    {
        $fillable = $this->model->getFillable();
        $attributes = array_intersect_key($attributes, array_flip($fillable));

        // This will respect the $fillable rules
        return User::create($attributes);
    }
}
