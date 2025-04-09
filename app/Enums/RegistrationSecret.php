<?php

namespace App\Enums;

/**
 * Enum representing different types of authentication secrets for registration.
 * Defines how users can authenticate during and after registration.
 *
 * @category App\Enums
 * @package  App\Enums
 * @author   John Doe <john.doe@example.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/your-repo
 */
enum RegistrationSecret: string
{
    /**
     * Password-based authentication
     */
    case PASSWORD = 'password';

    /**
     * One-time password (OTP) based authentication
     */
    case OTP = 'otp';

    /**
     * Check if this is a password-based authentication.
     *
     * @return bool
     */
    public function isPassword(): bool
    {
        return $this === self::PASSWORD;
    }

    /**
     * Check if this is an OTP-based authentication.
     *
     * @return bool
     */
    public function isOtp(): bool
    {
        return $this === self::OTP;
    }
}
