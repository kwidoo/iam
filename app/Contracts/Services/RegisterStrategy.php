<?php

namespace App\Contracts\Services;

use App\Data\RegistrationData;
use App\Models\User;

interface RegisterStrategy
{
    public function register(RegistrationData $data): User;

    public function method(): string;
}
