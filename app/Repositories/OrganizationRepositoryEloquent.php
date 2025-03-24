<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\Repositories\OrganizationRepository;
use App\Models\Organization;
use App\Validators\OrganizationValidator;

/**
 * Class OrganizationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrganizationRepositoryEloquent extends BaseRepository implements OrganizationRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Organization::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
