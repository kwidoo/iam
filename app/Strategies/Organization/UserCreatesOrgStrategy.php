<?php

namespace App\Strategies\Organization;

use App\Contracts\Services\OrganizationService;
use App\Contracts\Services\Strategy;
use App\Data\RegistrationData;
use App\Enums\RegistrationFlow;

class UserCreatesOrgStrategy implements Strategy
{
    public function __construct(protected OrganizationService $service) {}

    public function key(): RegistrationFlow
    {
        return RegistrationFlow::USER_CREATES_ORG;
    }


    /**
     * @param RegistrationData $data
     *
     * @return void
     */
    public function create(RegistrationData $data)
    {
        $data->flow = $this->key()->value;
        $data->organization = $this->service->createDefaultForUser($data);
    }
}
