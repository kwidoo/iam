<?php

namespace App\Services\Registration;

use Kwidoo\Mere\Contracts\Data\RegistrationData;

interface SecretInterface
{
    public function key(): string;

    public function create(RegistrationData $data);
}
