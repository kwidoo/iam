<?php

namespace App\Strategies\Organization;

use App\Contracts\Services\OrganizationCreateService;
use Kwidoo\Mere\Contracts\Data\RegistrationData;
use App\Enums\RegistrationFlow;
use App\Services\OrganizationService;

class UserJoinsUserOrg implements OrganizationCreateService
{
    /**
     * Initialize the strategy with required service.
     *
     * @param OrganizationService $service Organization service instance
     */
    public function __construct(
        protected OrganizationService $service,
    ) {}

    /**
     * Get the registration flow type this strategy handles.
     *
     * @return RegistrationFlow
     */
    public function key(): RegistrationFlow
    {
        return RegistrationFlow::USER_JOINS_USER_ORG;
    }

    /**
     * Connect the user to an existing organization during registration.
     * Validates the organization and sets up the user-organization relationship.
     *
     * @param RegistrationData $data Registration data containing user and org info
     *
     * @return void
     */
    public function create(RegistrationData $data): void
    {
        $data->organization = $this->service->connectToExistingOrg($data);
    }
}
