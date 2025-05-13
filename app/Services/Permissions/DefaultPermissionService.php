<?php

namespace App\Services\Permissions;

use App\Contracts\Services\PermissionService;
use App\Contracts\Repositories\PermissionRepository;
use App\Contracts\Services\OrganizationService;
use App\Criteria\ByOrganizationId;
use App\Services\BaseService;
use App\Services\Traits\RunsLifecycle;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;
use Kwidoo\Mere\Contracts\MenuService;
use Kwidoo\Mere\Data\ShowQueryData;
use Spatie\Permission\Contracts\Permission;

class DefaultPermissionService extends BaseService implements PermissionService
{
    use RunsLifecycle;

    public function __construct(
        MenuService $menuService,
        PermissionRepository $repository,
        Lifecycle $lifecycle,
        protected OrganizationService $organizationService,

    ) {
        parent::__construct($menuService, $repository, $lifecycle);
    }

    /**
     * @param string $name
     * @param string|null $organizationId
     *
     * @return Permission
     */
    public function getByName(string $name, ?string $organizationId = null): Permission|null
    {
        return $this->runLifecycle(
            context: ShowQueryData::from(id: $organizationId),
            callback: fn() => $this->handleGetByName($name, $organizationId)
        );
    }

    /**
     * @param string $name
     * @param string|null $organizationId
     *
     * @return Permission
     */
    public function handleGetByName(string $name, ?string $organizationId = null): Permission|null
    {
        if ($organizationId) {
            $this->repository->pushCriteria(new ByOrganizationId($organizationId));
        }

        return $this->repository->findByField('name', $name)->first();
    }

    /**
     * @return string
     */
    protected function eventKey(): string
    {
        return 'permission';
    }
}
