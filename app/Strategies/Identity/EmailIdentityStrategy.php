<?php

namespace App\Strategies\Identity;

use App\Contracts\Services\Strategy;
use Kwidoo\Contacts\Contracts\ContactService;
use App\Data\RegistrationData;

class EmailIdentityStrategy implements Strategy
{
    public function __construct(protected ContactService $service) {}

    public function key(): string
    {
        return 'email';
    }

    public function create(RegistrationData $data)
    {
        $this->service->create(
            $data->method,
            $data->value,
        );
    }
}
