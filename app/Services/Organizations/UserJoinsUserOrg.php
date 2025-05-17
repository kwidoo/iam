<?php

namespace App\Services\Organizations;

use App\Contracts\Services\OrganizationCreateService;
use App\Contracts\Services\Organizations\ConnectProfileService;
use App\Contracts\Services\Organizations\ConnectUserService;
use App\Contracts\Services\Organizations\OrganizationAccessService;
use App\Contracts\Services\Organizations\OrganizationService;
use App\Enums\OrganizationFlow;
use App\Services\Permissions\StandardUserPermissionAssignment;
use App\Services\Roles\StandardUserRoleAssignment;
use Kwidoo\Mere\Contracts\Models\OrganizationInterface;
use Spatie\LaravelData\Contracts\BaseData;

class UserJoinsUserOrg implements OrganizationCreateService
{
    /**
     * Initialize the strategy with required service.
     *
     * @param OrganizationService $service Organization service instance
     */
    public function __construct(
        protected OrganizationService $service,
        protected StandardUserRoleAssignment $roleAssignment,
        protected StandardUserPermissionAssignment $permissionAssignment,
        protected ConnectUserService $connectUserService,
        protected ConnectProfileService $connectProfileService
    ) {}

    /**
     * Get the registration flow type this strategy handles.
     *
     * @return OrganizationFlow
     */
    public function key(): OrganizationFlow
    {
        return OrganizationFlow::USER_JOINS_USER_ORG;
    }

    /**
     * Connect the user to an existing organization during registration.
     * Validates the organization and sets up the user-organization relationship.
     *
     * @param \App\Data\Organizations\OrganizationCreateData $data Registration data containing user and org info
     *
     * @return \App\Models\Organization
     */
    public function create(BaseData $data): OrganizationInterface
    {
        $data->flow =  $this->key();
        $organization = $this->service->connect($data);
        return $organization;
    }
}
