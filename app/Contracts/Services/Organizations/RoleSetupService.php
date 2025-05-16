<?php

namespace App\Contracts\Services\Organizations;

use Kwidoo\Mere\Contracts\Models\OrganizationInterface;
use Spatie\LaravelData\Contracts\BaseData;

interface RoleSetupService
{
    /**
     * Initialize roles and permissions for an organization.
     *
     * @param \App\Models\Organization $organization The organization to initialize roles for
     * @param \App\Data\Organizations\OrganizationCreateData $data The registration data
     * @return void
     */
    public function initialize(OrganizationInterface $organization, BaseData $data): void;
}
