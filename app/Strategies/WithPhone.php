<?php

namespace App\Strategies;

use App\Contracts\Services\RegisterStrategy;
use App\Contracts\Services\RegistrationService;
use App\Data\RegistrationData;
use App\Models\User;

class WithPhone implements RegisterStrategy
{
    public function __construct(
        protected RegistrationService $service
    ) {}

    public function method(): string
    {
        return 'phone';
    }

    public function register(RegistrationData $data): User
    {
        return $this->service->registerNewUser($data);
    }
}
