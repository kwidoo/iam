<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class ByOrganizationIdCriteria.
 *
 * @package namespace App\Criteria;
 */
class ByOrganizationId implements CriteriaInterface
{
    /**
     * The organization ID
     *
     * @var int
     */
    public function __construct(
        protected int|string $organizationId,
    ) {}
    /**
     * Apply criteria in query repository
     *
     * @param App\Models\Organization $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        return $model->where('organization_id', $this->organizationId);
    }
}
