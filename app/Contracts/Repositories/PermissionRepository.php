<?php

namespace App\Contracts\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface for the Permission repository.
 */
interface PermissionRepository extends RepositoryInterface
{
    /**
     * Find permissions by organization ID.
     *
     * @param int $organizationId
     * @return mixed
     */
    public function findByOrganizationId(int $organizationId);

    /**
     * Find permissions by names and organization ID.
     *
     * @param array $names
     * @param int $organizationId
     * @return mixed
     */
    public function findByNamesAndOrganization(array $names, int $organizationId);

    /**
     * Create permission with given name, guard name and organization ID.
     *
     * @param string $name
     * @param string $guardName
     * @param int $organizationId
     * @return mixed
     */
    public function createWithOrganization(string $name, string $guardName, int $organizationId);

    /**
     * Find or create permissions for a specific organization.
     *
     * @param array $resourceActions Array of permission names to create ['view_users', 'edit_profiles', etc]
     * @param int $organizationId The organization ID
     * @param string $guardName The guard name (default: web)
     * @return array The created/found permissions
     */
    public function findOrCreateForOrganization(array $resourceActions, int $organizationId, string $guardName = 'web');
}
