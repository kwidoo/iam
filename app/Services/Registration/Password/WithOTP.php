<?php

namespace App\Services\Registration\Password;

use Kwidoo\Mere\Contracts\Data\RegistrationData;
use App\Services\Registration\SecretInterface;

class WithOTP implements SecretInterface
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
    public function create(RegistrationData $data)
    {
        //
    }
}
