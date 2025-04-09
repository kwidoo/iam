<?php

namespace App\Strategies\Organization;

use App\Contracts\Services\OrganizationService;
use App\Contracts\Services\Strategy;
use App\Data\RegistrationData;
use App\Enums\RegistrationFlow;

/**
 * Strategy for handling user registration where the user creates a new organization.
 * Implements the organization creation flow during user registration.
 *
 * @category App\Strategies\Organization
 * @package  App\Strategies\Organization
 * @author   John Doe <john.doe@example.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/your-repo
 */
class UserCreatesOrgStrategy implements Strategy
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
        $data->organization = $this->service->createDefaultForUser($data);
    }
}
