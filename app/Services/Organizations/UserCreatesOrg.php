<?php

namespace App\Strategies\Organization;

use App\Contracts\Services\OrganizationCreateService;
use App\Contracts\Services\OrganizationService;
use Kwidoo\Mere\Contracts\Data\RegistrationData;
use App\Enums\RegistrationFlow;

class UserCreatesOrg implements OrganizationCreateService
{
    /**
     * Initialize the strategy with required service.
     *
     * @param OrganizationService $service Organization service instance
     */
    public function __construct(protected OrganizationService $service) {}

    /**
     * Get the registration flow type this strategy handles.
     *
     * @return RegistrationFlow
     */
    public function key(): RegistrationFlow
    {
        return RegistrationFlow::USER_CREATES_ORG;
    }

    /**
     * Create a new organization for the user during registration.
     * Sets up the organization with default settings and associates the user.
     *
     * @param RegistrationData $data Registration data containing user and org info
     *
     * @return void
     */
    public function create(RegistrationData $data): void
    {
        $data->flow = $this->key()->value;
        $data->organization = $this->service->create($data);
    }
}
