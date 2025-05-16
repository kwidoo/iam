<?php

namespace App\Contracts\Services\Registration;

use Kwidoo\Mere\Contracts\Data\RegistrationData;
use Kwidoo\Mere\Contracts\Services\BaseService;
use App\Models\User;
use Spatie\LaravelData\Contracts\BaseData;

interface RegistrationService extends BaseService
{
    /**
     * @param BaseData $data
     *
     * @return User
     */
    public function registerNewUser(BaseData $data): User;
}
