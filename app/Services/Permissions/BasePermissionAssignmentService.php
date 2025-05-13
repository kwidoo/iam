<?php

namespace App\Services\Permissions;

use App\Contracts\Services\PermissionService;
use App\Data\GivePermissionData;
use App\Services\Organizations\UserAndOrganizationResolver;
use App\Services\Traits\RunsLifecycle;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;


abstract class BasePermissionAssignmentService implements PermissionAssignment
{
    use RunsLifecycle;

    abstract protected string $permissionType;

    public function __construct(
        protected Lifecycle $lifecycle,
        protected PermissionService $service,
        protected PermissionNameResolver $nameResolver,
        protected UserAndOrganizationResolver $resolver,

    ) {
        //
    }

    /**
     * @param GivePermissionData $data
     *
     * @return void
     */
    public function assign(GivePermissionData $data): void
    {
        $this->runLifecycle(
            context: $data,
            callback: fn() => $this->handleAssign($data)
        );
    }

    /**
     * @param GivePermissionData $data
     *
     * @return void
     */
    public function revoke(GivePermissionData $data): void
    {
        $this->runLifecycle(
            context: $data,
            callback: fn() => $this->handleRevoke($data)
        );
    }

    /**
     * @param GivePermissionData $data
     *
     * @return void
     */
    protected function handleAssign(GivePermissionData $data): void
    {
        [$user, $organization] = $this->resolver->resolve(
            userId: $data->userId,
            organizationId: $data->organizationId
        );

        $name = $this->nameResolver->resolve(
            permission: $this->permissionType,
            organizationSlug: $organization->slug,
        );

        $permission = $this->service->getByName($name, $organization->id);

        $user->givePermissionTo($permission);
    }

    /**
     * @param GivePermissionData $data
     *
     * @return void
     */
    protected function handleRevoke(GivePermissionData $data): void
    {
        [$user, $organization] = $this->resolver->resolve(
            userId: $data->userId,
            organizationId: $data->organizationId
        );

        $name = $this->nameResolver->resolve(
            permission: $this->permissionType,
            organizationSlug: $organization->slug,
        );

        $permission = $this->service->getByName($name, $organization->id);


        $user->revokePermissionTo($permission);
    }

    /**
     * @return string
     */
    protected function eventKey(): string
    {
        return "{$this->permissionType}.permission";
    }
}
