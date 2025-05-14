<?php

namespace App\Strategies\Organization;

use App\Contracts\Services\OrganizationCreateService;
use App\Contracts\Services\OrganizationService;
use Kwidoo\Mere\Contracts\Data\RegistrationData;
use App\Enums\RegistrationFlow;


class MainOnly implements OrganizationCreateService
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
        return RegistrationFlow::MAIN_ONLY;
    }

    /**
     * Load the default organization for the user during registration.
     * Associates the user with the main organization if it exists.
     *
     * @param RegistrationData $data Registration data containing user info
     *
     * @return void
     */
    public function create(RegistrationData $data): void
    {
        $data->organization = $this->service->loadDefault($data);
    }
}
