<?php

namespace App\Criteria\Invitations;

use App\Models\Invitation;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class PendingInvitations.
 *
 * Filter invitations with pending status.
 *
 * @package namespace App\Criteria\Invitations;
 */
class PendingInvitations implements CriteriaInterface
{
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
        return $model->where('status', Invitation::STATUS_PENDING);
    }
}
