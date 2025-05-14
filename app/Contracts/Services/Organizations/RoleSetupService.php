<?php

namespace App\Contracts\Services\Organizations;

use Kwidoo\Mere\Contracts\Models\OrganizationInterface;
use Kwidoo\Mere\Contracts\Data\RegistrationData;

interface RoleSetupService
{
    /**
     * Initialize roles and permissions for an organization.
     *
     * @param \App\Models\Organization $organization The organization to initialize roles for
     * @param \App\Data\Registration\RegistrationData $data The registration data
     * @return void
     */
    public function initialize(OrganizationInterface $organization, RegistrationData $data): void;
}
