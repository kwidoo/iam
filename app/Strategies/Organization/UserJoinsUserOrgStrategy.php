<?php

namespace App\Strategies\Organization;

use App\Contracts\Services\Strategy;
use App\Data\RegistrationData;
use App\Enums\RegistrationFlow;
use App\Services\OrganizationService;

class UserJoinsUserOrgStrategy implements Strategy
{
    public function __construct(
        protected OrganizationService $service,
    ) {}

    public function key(): RegistrationFlow
    {
        return RegistrationFlow::USER_JOINS_USER_ORG;
    }

    public function create(RegistrationData $data): void
    {
        $data->organization = $this->service->connectToExistingOrg($data);
    }
}
