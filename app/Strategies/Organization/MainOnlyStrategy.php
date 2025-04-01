<?php

namespace App\Strategies\Organization;

use App\Contracts\Services\OrganizationService;
use App\Contracts\Services\Strategy;
use App\Data\RegistrationData;

class MainOnlyStrategy implements Strategy
{
    public function __construct(protected OrganizationService $service) {}

    public function key(): string
    {
        return 'main_only';
    }

    public function create(RegistrationData $data)
    {
        $data->organization = $this->service->loadDefault($data);
    }
}
