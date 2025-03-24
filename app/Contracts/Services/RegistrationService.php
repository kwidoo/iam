<?php

namespace App\Contracts\Services;

use Kwidoo\Mere\Contracts\BaseService;

interface RegistrationService extends BaseService
{
    public function registerNewUser(array $data);
}
