<?php

namespace App\Strategies\Organization;

use App\Contracts\Services\Strategy;
use App\Data\RegistrationData;
use App\Enums\RegistrationFlow;
use App\Services\OrganizationService;

/**
 * Strategy for handling user registration where the user joins an existing organization.
 * Implements the organization joining flow during user registration.
 *
 * @category App\Strategies\Organization
 * @package  App\Strategies\Organization
 * @author   John Doe <john.doe@example.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/your-repo
 */
class UserJoinsUserOrgStrategy implements Strategy
{
    /**
     * Initialize the strategy with required service.
     *
     * @param OrganizationService $service Organization service instance
     */
    public function __construct(
        protected OrganizationService $service,
    ) {
    }

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
