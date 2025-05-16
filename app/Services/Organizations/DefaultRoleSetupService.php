<?php

namespace App\Services\Organizations;

use App\Contracts\Services\Organizations\RoleSetupService;
use App\Contracts\Services\PermissionService;
use App\Contracts\Services\Roles\RoleService;
use App\Data\Permissions\PermissionCreateData;
use App\Data\Roles\RoleCreateData;
use Kwidoo\Mere\Contracts\Models\OrganizationInterface;
use App\Services\Permissions\PermissionNameResolver;
use App\Services\Roles\RoleNameResolver;
use Kwidoo\Lifecycle\Traits\RunsLifecycle;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;
use Spatie\LaravelData\Contracts\BaseData;

class DefaultRoleSetupService implements RoleSetupService
{
    use RunsLifecycle;

    public function __construct(
        protected Lifecycle $lifecycle,
        protected RoleService $roleService,
        protected RoleNameResolver $rnr,
        protected PermissionService $permissionService,
        protected PermissionNameResolver $pnr,
    ) {}

    /**
     * @param \App\Models\Organization $organization
     * @param \App\Data\Organizations\OrganizationCreateData $data
     *
     * @return void
     */
    public function initialize(OrganizationInterface $organization, BaseData $data): void
    {
        $this->runLifecycle(
            context: $data,
            callback: fn() => $this->createDefaults($organization),
        );
    }

    /**
     * @param \App\Models\Organization $organization
     *
     * @return void
     */
    protected function createDefaults(OrganizationInterface $organization): void
    {
        $this->createFor('admin', $organization, 'api');
        $this->createFor('user', $organization, 'api');
    }

    /**
     * @param string $role
     * @param \App\Models\Organization $organization
     * @param string $guardName
     *
     * @return void
     */
    protected function createFor(string $role, OrganizationInterface $organization, string $guardName = 'api'): void
    {

        $this->roleService->create(RoleCreateData::from([
            'name' => $this->rnr->resolve($role, $organization->slug),
            'organization_id' => $organization->id,
            'guard_name' => $guardName,
        ]));

        $this->permissionService->create(PermissionCreateData::from([
            'name' => $this->pnr->resolve($role, $organization->slug),
            'organization_id' => $organization->id,
            'guard_name' => $guardName,
        ]));
    }

    /**
     * @return string
     */
    protected function eventKey(): string
    {
        return 'organization_roles';
    }
}
