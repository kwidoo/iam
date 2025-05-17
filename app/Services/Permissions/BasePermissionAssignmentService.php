<?php

namespace App\Services\Permissions;

use App\Contracts\Services\PermissionService;
use App\Data\Organizations\UserOrganizationData;
use App\Data\Permissions\GivePermissionData;
use App\Services\Organizations\UserAndOrganizationResolver;
use Kwidoo\Lifecycle\Traits\RunsLifecycle;

use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;


abstract class BasePermissionAssignmentService implements PermissionAssignment
{
    use RunsLifecycle;


    public function __construct(
        protected Lifecycle $lifecycle,
        protected PermissionService $service,
        protected PermissionNameResolver $nameResolver,
    ) {
        //
    }

    /**
     * @param UserOrganizationData $data
     *
     * @return void
     */
    public function assign(UserOrganizationData $data): void
    {
        $this->runLifecycle(
            context: $data,
            callback: fn() => $this->handleAssign($data)
        );
    }

    /**
     * @param UserOrganizationData $data
     *
     * @return void
     */
    public function revoke(UserOrganizationData $data): void
    {
        $this->runLifecycle(
            context: $data,
            callback: fn() => $this->handleRevoke($data)
        );
    }

    /**
     * @param UserOrganizationData $data
     *
     * @return void
     */
    protected function handleAssign(UserOrganizationData $data): void
    {
        $name = $this->nameResolver->resolve(
            permission: $this->permissionType(),
            organizationSlug: $data->organization->slug,
        );

        $permission = $this->service->getByName($name, $data->organization->id);

        $data->user->givePermissionTo($permission);
    }

    /**
     * @param UserOrganizationData $data
     *
     * @return void
     */
    protected function handleRevoke(UserOrganizationData $data): void
    {

        $name = $this->nameResolver->resolve(
            permission: $this->permissionType(),
            organizationSlug: $data->organization->slug,
        );

        $permission = $this->service->getByName($name, $data->organization->id);

        $data->user->revokePermissionTo($permission);
    }

    abstract public function permissionType(): string;

    /**
     * @return string
     */
    protected function eventKey(): string
    {
        $name = $this->permissionType();
        return "$name.permission";
    }
}
