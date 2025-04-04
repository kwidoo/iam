<?php

namespace App\Contracts\Repositories;

use App\Models\Invitation;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface InvitationRepository.
 *
 * @package namespace App\Contracts\Repositories;
 */
interface InvitationRepository extends RepositoryInterface
{
    public function findByToken(string $token): Invitation|null;
}
