<?php

namespace App\Criteria\Invitations;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class ByInviterId.
 *
 * Filter invitations by inviter's ID.
 *
 * @package namespace App\Criteria\Invitations;
 */
class ByInviterId implements CriteriaInterface
{
    /**
     * @param string|int $inviterId The ID of the inviter
     */
    public function __construct(
        protected string|int $inviterId
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
        return $model->where('inviter_id', $this->inviterId);
    }
}
