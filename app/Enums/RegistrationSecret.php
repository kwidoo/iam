<?php

namespace App\Enums;

enum RegistrationSecret: string
{
    case PASSWORD = 'password';
    case OTP = 'otp';

    public function isPassword(): bool
    {
        return $this === self::PASSWORD;
    }

    public function isOtp(): bool
    {
        return $this === self::OTP;
    }
}
