<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Kwidoo\Mere\Contracts\Repositories\OrganizationRepository;
use App\Validators\OrganizationValidator;
use Kwidoo\Mere\Contracts\Models\OrganizationInterface;
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
        return OrganizationInterface::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getMainOrganization(): ?OrganizationInterface
    {
        return $this->where('slug', 'main')->first();
    }
}
