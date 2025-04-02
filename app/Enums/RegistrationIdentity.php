<?php

namespace App\Enums;

enum RegistrationIdentity: string
{
    case EMAIL = 'email';
    case PHONE = 'phone';

    public function isEmail(): bool
    {
        return $this === self::EMAIL;
    }
    public function isPhone(): bool
    {
        return $this === self::PHONE;
    }
}
