<?php

namespace App\Strategies\Organization;

use App\Contracts\Services\OrganizationService;
use App\Contracts\Services\Strategy;
use App\Data\RegistrationData;
use App\Enums\RegistrationFlow;

class MainOnlyStrategy implements Strategy
{
    public function __construct(protected OrganizationService $service) {}

    public function key(): RegistrationFlow
    {
        return RegistrationFlow::MAIN_ONLY;
    }


    public function create(RegistrationData $data)
    {
        $data->organization = $this->service->loadDefault($data);
    }
}
