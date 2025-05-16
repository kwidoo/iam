<?php

namespace App\Criteria\Invitations;

use App\Models\Invitation;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class ByStatus.
 *
 * Filter invitations by their status (pending, accepted, etc).
 *
 * @package namespace App\Criteria\Invitations;
 */
class ByStatus implements CriteriaInterface
{
    /**
     * @param string $status The invitation status (pending, accepted, rejected, expired)
     */
    public function __construct(
        protected string $status = Invitation::STATUS_PENDING
    ) {}

    /**
     * Apply criteria in query repository
     *
     * @param mixed $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        return $model->where('status', $this->status);
    }
}
