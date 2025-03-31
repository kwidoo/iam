<?php

namespace App\Contracts\Services;

use App\Data\RegistrationData;
use App\Models\Profile;
use Kwidoo\Mere\Contracts\BaseService;

interface ProfileService extends BaseService
{
    public function findByUserId(string $userId): Profile;

    public function registerProfile(RegistrationData $data): mixed;
}
