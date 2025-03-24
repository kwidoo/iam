<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\Repositories\MicroserviceRepository;
use App\Models\Microservice;
use App\Validators\MicroserviceValidator;

/**
 * Class MicroserviceRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class MicroserviceRepositoryEloquent extends BaseRepository implements MicroserviceRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Microservice::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
