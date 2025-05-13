<?php


namespace App\Services\Roles;

use App\Contracts\Services\Roles\RoleAssignment;
use App\Contracts\Services\Roles\RoleService;
use App\Data\RoleAssignmentData;
use App\Services\Organizations\UserAndOrganizationResolver;
use App\Services\Traits\RunsLifecycle;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;

abstract class BaseRoleAssignmentService implements RoleAssignment
{
    use RunsLifecycle;

    abstract protected string $roleType;

    public function __construct(
        protected RoleService $service,
        protected Lifecycle $lifecycle,
        protected RoleNameResolver $nameResolver,
        protected UserAndOrganizationResolver $resolver,
    ) {
        //
    }

    /**
     * @param RoleAssignmentData $data
     *
     * @return void
     */
    public function assign(RoleAssignmentData $data): void
    {
        $this->runLifecycle(
            context: $data,
            callback: fn() => $this->handleAssign($data)
        );
    }

    /**
     * @param RoleAssignmentData $data
     *
     * @return void
     */
    public function revoke(RoleAssignmentData $data): void
    {
        $this->runLifecycle(
            context: $data,
            callback: fn() => $this->handleRevoke($data)
        );
    }

    /**
     * @param RoleAssignmentData $data
     *
     * @return void
     */
    protected function handleAssign(RoleAssignmentData $data): void
    {
        [$user, $organization] = $this->resolver->resolve(
            userId: $data->userId,
            organizationId: $data->organizationId
        );

        $name = $this->nameResolver->resolve(
            role: $this->roleType,
            organizationSlug: $organization->slug,
        );
        $role = $this->service->getByName($name, $organization->id);

        $user->assignRole($role, $user->id, $organization->id);
    }

    /**
     * @param RoleAssignmentData $data
     *
     * @return void
     */
    protected function handleRevoke(RoleAssignmentData $data): void
    {
        [$user, $organization] = $this->resolver->resolve(
            userId: $data->userId,
            organizationId: $data->organizationId
        );

        $name = $this->nameResolver->resolve(
            role: $this->roleType,
            organizationSlug: $organization->slug,
        );
        $role = $this->service->getByName(
            name: $name,
            organizationId: $organization->id
        );

        $user->removeRole($role);
    }

    /**
     * @return string
     */
    protected function eventKey(): string
    {
        return "{$this->roleType}.role";
    }
}
