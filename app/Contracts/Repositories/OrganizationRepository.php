<?php

namespace App\Contracts\Repositories;

use App\Models\Organization;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface OrganizationRepository.
 *
 * @package namespace App\Contracts\Repositories;
 */
interface OrganizationRepository extends RepositoryInterface
{
    public function getMainOrganization(): ?Organization;
}
