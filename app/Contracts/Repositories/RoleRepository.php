<?php

namespace App\Contracts\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface for the Role repository.
 */
interface RoleRepository extends RepositoryInterface
{
    /**
     * Find roles by organization ID.
     *
     * @param int $organizationId
     * @return mixed
     */
    public function findByOrganizationId(int $organizationId);

    /**
     * Find role by name and organization ID.
     *
     * @param string $name
     * @param int $organizationId
     * @return mixed
     */
    public function findByNameAndOrganization(string $name, int $organizationId);

    /**
     * Create role with given name, guard name and organization ID.
     *
     * @param string $name
     * @param string $guardName
     * @param int $organizationId
     * @return mixed
     */
    public function createWithOrganization(string $name, string $guardName, int $organizationId);
}
