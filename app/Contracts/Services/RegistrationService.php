<?php

namespace App\Contracts\Services;

use App\Data\RegistrationData;
use Kwidoo\Mere\Contracts\BaseService;

interface RegistrationService extends BaseService
{
    public function registerNewUser(RegistrationData $data);
}
