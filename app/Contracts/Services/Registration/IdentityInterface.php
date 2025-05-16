<?php

namespace App\Services\Registration;

use Kwidoo\Mere\Contracts\Data\RegistrationData;

interface IdentityInterface
{
    public function key(): string;

    public function create(RegistrationData $data);
}
