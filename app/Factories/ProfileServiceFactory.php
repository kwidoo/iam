<?php

namespace App\Factories;

use App\Contracts\Services\ProfileService;
use App\Models\User;

class ProfileServiceFactory
{
    public function make(User $user): ProfileService
    {
        return app()->make(ProfileService::class, ['user' => $user]);
    }
}
