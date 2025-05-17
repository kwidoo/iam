<?php

namespace App\Services\Organizations;

use App\Contracts\Services\Organizations\OrganizationAccessService;
use App\Contracts\Services\PermissionService;
use App\Contracts\Services\Roles\RoleAssignment;
use App\Contracts\Services\Roles\RoleService;
use App\Data\Organizations\UserOrganizationData;
use App\Data\Roles\RoleAssignmentData;
use App\Services\Permissions\PermissionAssignment;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;
use Kwidoo\Lifecycle\Traits\RunsLifecycle;
use Kwidoo\Mere\Contracts\Repositories\OrganizationRepository;
use Kwidoo\Mere\Services\BaseService;
use Kwidoo\Mere\Contracts\Services\MenuService;
use Spatie\Permission\Contracts\Permission;

class DefaultOrganizationAccessService extends BaseService implements OrganizationAccessService
{
    use RunsLifecycle;

    public function __construct(
        MenuService $menuService,
        OrganizationRepository $repository,
        Lifecycle $lifecycle,
        protected RoleAssignment $roleAssignment,
        protected PermissionAssignment $permissionAssignment,
    ) {
        parent::__construct($menuService, $repository, $lifecycle);
    }

    /**
     * @param UserOrganizationData $data
     *
     * @return void
     */
    public function provide(UserOrganizationData $data): void
    {
        $this->runLifecycle(
            context: $data,
            callback: fn() => $this->handleProvide($data)
        );
    }

    /**
     * @param UserOrganizationData $data
     *
     * @return void
     */
    protected function handleProvide(UserOrganizationData $data): void
    {
        $this->permissionAssignment->assign($data);
        $this->roleAssignment->give($data);
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
    protected function handleRevoke(UserOrganizationData $data): void
    {
        //  $this->repository->detachUser($data->organization, $data->user);
        //  $this->repository->syncPermissions($data->organization, $data->user);
    }

    protected function eventKey(): string
    {
        return 'organization_user_permission';
    }
}
