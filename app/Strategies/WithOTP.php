<?php

namespace App\Strategies;

use App\Contracts\Services\PasswordStrategy;

class WithOTP implements PasswordStrategy
{
    /**
     * @param string|null $value
     *
     * @return string|null
     */
    public function password(?string $value = null): ?string
    {
        return null;
    }

    public function method(): string
    {
        return 'otp';
    }
}
