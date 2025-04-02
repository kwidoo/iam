<?php

namespace App\Contracts\Services;

use App\Data\RegistrationData;
use App\Enums\RegistrationFlow;

interface Strategy
{
    public function key(): RegistrationFlow|string;

    public function create(RegistrationData $data);
}
