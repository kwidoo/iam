<?php

namespace App\Strategies\Organization;

use App\Contracts\Services\OrganizationService;
use App\Contracts\Services\Strategy;
use App\Data\RegistrationData;
use App\Enums\RegistrationFlow;

/**
 * Strategy for handling user registration without organization association.
 * Implements the main-only registration flow where users are not associated with any organization.
 *
 * @category App\Strategies\Organization
 * @package  App\Strategies\Organization
 * @author   John Doe <john.doe@example.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/your-repo
 */
class MainOnlyStrategy implements Strategy
{
    /**
     * Initialize the strategy with required service.
     *
     * @param OrganizationService $service Organization service instance
     */
    public function __construct(protected OrganizationService $service)
    {
    }

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
