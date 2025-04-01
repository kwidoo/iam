<?php

namespace App\Strategies\Organization;

use App\Contracts\Services\OrganizationService;
use App\Contracts\Services\Strategy;
use App\Data\RegistrationData;

class UserCreatesOrgStrategy implements Strategy
{
    public function __construct(protected OrganizationService $service) {}

    public function key(): string
    {
        return 'user_creates_org';
    }

    public function create(RegistrationData $data)
    {
        $data->organization = $this->service->createDefaultForUser($data);
    }
}
