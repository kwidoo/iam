<?php

namespace App\Criteria\Invitations;

use App\Models\Invitation;
use Carbon\Carbon;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class ExpiredInvitations.
 *
 * Filter invitations that have expired based on their expiration date.
 * Used to cleanup expired invitations.
 *
 * @package namespace App\Criteria\Invitations;
 */
class ExpiredInvitations implements CriteriaInterface
{
    /**
     * @param Carbon|null $referenceDate Optional reference date, defaults to now
     */
    public function __construct(
        protected ?Carbon $referenceDate = null
    ) {
        $this->referenceDate = $referenceDate ?? Carbon::now();
    }

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
        return $model->where('status', Invitation::STATUS_PENDING)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', $this->referenceDate);
    }
}
