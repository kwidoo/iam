<?php

namespace App\Contracts\Services\Registration;

use Kwidoo\Mere\Contracts\Data\RegistrationData;
use App\Models\User;
use Spatie\LaravelData\Contracts\BaseData;

interface RegistrationService
{
    /**
     * @param BaseData $data
     *
     * @return User
     */
    public function registerNewUser(BaseData $data): User;
}
