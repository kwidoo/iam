<?php

namespace App\Criteria\Invitations;

use App\Models\Invitation;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class ValidByToken.
 *
 * Filter invitations by token and validity (non-expired, non-accepted).
 * Used to retrieve valid invitations when accepting them.
 *
 * @package namespace App\Criteria\Invitations;
 */
class ValidByToken implements CriteriaInterface
{
    /**
     * @param string $token The invitation token
     */
    public function __construct(
        protected string $token
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
        return $model->where('token', $this->token)
            ->where('status', Invitation::STATUS_PENDING)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }
}
