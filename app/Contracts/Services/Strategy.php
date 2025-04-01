<?php

namespace App\Contracts\Services;

use App\Data\RegistrationData;

interface Strategy
{
    public function key(): string;

    public function create(RegistrationData $data);
}
