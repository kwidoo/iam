<?php

namespace App\Strategies\Password;

use App\Contracts\Services\Strategy;
use App\Data\RegistrationData;

class WithOTP implements Strategy
{
    /**
     * @return string
     */
    public function key(): string
    {
        return 'otp';
    }

    /**
     * @param string|null $value
     *
     * @return string|null
     */
    public function create(RegistrationData $data) {}
}
