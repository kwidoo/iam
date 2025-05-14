<?php

namespace App\Services\Organizations;

use App\Contracts\Services\Organizations\RoleSetupService;
use App\Contracts\Services\PermissionService;
use App\Contracts\Services\Roles\RoleService;
use Kwidoo\Mere\Contracts\Data\RegistrationData;
use App\Data\Permissions\PermissionCreateData;
use App\Data\Roles\RoleCreateData;
use Kwidoo\Mere\Contracts\Models\OrganizationInterface;
use App\Services\Permissions\PermissionNameResolver;
use App\Services\Roles\RoleNameResolver;
use Kwidoo\Lifecycle\Traits\RunsLifecycle;

use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;

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
     * @param \App\Data\Registration\DefaultRegistrationData $registrationData
     *
     * @return void
     */
    public function initialize(OrganizationInterface $organization, RegistrationData $registrationData): void
    {
        $this->runLifecycle(
            context: $registrationData,
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
        $this->runLifecycle(
            context: ['organization' => $organization],
            callback: function () use ($organization) {
                $this->createFor('admin', $organization);
                $this->createFor('user', $organization);
            }
        );
    }

    /**
     * @param string $role
     * @param \App\Models\Organization $organization
     * @param string $guardName
     *
     * @return void
     */
    protected function createFor(string $role, OrganizationInterface $organization, string $guardName = 'web'): void
    {
        $this->runLifecycle(
            context: ['role' => $role, 'organization' => $organization],
            callback: function () use ($role, $organization, $guardName) {
                $this->roleService->create(RoleCreateData::from([
                    'name' => $this->rnr->resolve($role, $organization->getSlug()),
                    'organization_id' => $organization->getId(),
                    'description' => "Default {$role} role for {$organization->getName()}.",
                    'guard_name' => $guardName,
                ]));

                $this->permissionService->create(PermissionCreateData::from([
                    'name' => $this->pnr->resolve($role, $organization->getSlug()),
                    'organization_id' => $organization->getId(),
                    'description' => "Default {$role} permissions for {$organization->getName()}.",
                    'guard_name' => $guardName,
                ]));
            }
        );
    }

    /**
     * @return string
     */
    protected function eventKey(): string
    {
        return 'organization_roles';
    }
}
