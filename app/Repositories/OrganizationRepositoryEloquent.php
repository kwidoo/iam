<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\Repositories\OrganizationRepository;
use App\Models\Organization;
use App\Validators\OrganizationValidator;
use Kwidoo\Mere\Repositories\RepositoryEloquent;

/**
 * Class OrganizationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrganizationRepositoryEloquent extends RepositoryEloquent implements OrganizationRepository
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

    public function getMainOrganization(): ?Organization
    {
        return $this->where('slug', 'main')->first();
    }
}
