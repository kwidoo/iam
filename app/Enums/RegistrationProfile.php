<?php

namespace App\Enums;

enum RegistrationProfile: string
{
    case DEFAULT_PROFILE = 'default_profile';

    public function isDefaultProfile(): bool
    {
        return $this === self::DEFAULT_PROFILE;
    }
}
