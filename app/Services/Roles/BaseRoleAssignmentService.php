<?php


namespace App\Services\Roles;

use App\Contracts\Services\Roles\RoleAssignment;
use App\Contracts\Services\Roles\RoleService;
use App\Data\Organizations\UserOrganizationData;
use Kwidoo\Lifecycle\Traits\RunsLifecycle;

use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;

abstract class BaseRoleAssignmentService implements RoleAssignment
{
    use RunsLifecycle;

    public function __construct(
        protected RoleService $service,
        protected Lifecycle $lifecycle,
        protected RoleNameResolver $nameResolver,
    ) {
        //
    }

    /**
     * @param UserOrganizationData $data
     *
     * @return void
     */
    public function give(UserOrganizationData $data): void
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
            role: $this->roleType(),
            organizationSlug: $data->organization->slug,
        );
        $role = $this->service->getByName($name, $data->organization->id);

        $data->user->assignRole($role);
    }

    /**
     * @param UserOrganizationData $data
     *
     * @return void
     */
    protected function handleRevoke(UserOrganizationData $data): void
    {
        $name = $this->nameResolver->resolve(
            role: $this->roleType(),
            organizationSlug: $data->organization->slug,
        );
        $role = $this->service->getByName(
            name: $name,
            organizationId: $data->organization->id
        );

        $data->user->removeRole($role);
    }

    /**
     * @return string
     */
    protected function eventKey(): string
    {
        $name = $this->roleType();
        return "$name.role";
    }

    abstract public function roleType(): string;
}
