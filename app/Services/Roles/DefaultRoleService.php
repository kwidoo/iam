<?php

namespace App\Services\Roles;

use App\Contracts\Repositories\RoleRepository;
use App\Contracts\Services\Roles\RoleService;
use App\Criteria\ByOrganizationId;
use App\Services\BaseService;
use App\Services\Traits\RunsLifecycle;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;
use Kwidoo\Mere\Contracts\MenuService;
use Kwidoo\Mere\Data\ShowQueryData;
use Spatie\Permission\Contracts\Role;

class DefaultRoleService extends BaseService implements RoleService
{
    use RunsLifecycle;

    public function __construct(
        MenuService $menuService,
        RoleRepository $repository,
        Lifecycle $lifecycle,
    ) {
        parent::__construct($menuService, $repository, $lifecycle);
    }

    /**
     * @param string $name
     * @param string|null $organizationId
     *
     * @return Role|null
     */
    public function getByName(string $name, ?string $organizationId = null): Role|null
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
     * @return Role|null
     */
    public function handleGetByName(string $name, ?string $organizationId = null): Role|null
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
        return 'role';
    }
}
