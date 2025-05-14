<?php

namespace App\Contracts\Services\Organizations;

use Kwidoo\Mere\Contracts\Models\OrganizationInterface;
use Kwidoo\Mere\Contracts\Data\RegistrationData;
use Kwidoo\Mere\Contracts\Services\BaseService;

/**
 * Interface for organization-related operations.
 */
interface OrganizationService extends BaseService
{
    /**
     * Load the default organization for a registration data or create one if it doesn't exist.
     *
     * @param \App\Data\Registration\RegistrationData $data The registration data
     * @return \App\Models\Organization
     */
    public function loadDefault(RegistrationData $data): OrganizationInterface;

    /**
     * Connect a user to an existing organization.
     *
     * @param \App\Data\Registration\RegistrationData $data The registration data
     * @return \App\Models\Organization
     */
    public function connectToExistingOrg(RegistrationData $data): OrganizationInterface;
}
