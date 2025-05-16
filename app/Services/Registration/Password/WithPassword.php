<?php

namespace App\Services\Registration\Password;

use Kwidoo\Mere\Contracts\Data\RegistrationData;
use App\Services\Registration\SecretInterface;
use Illuminate\Support\Facades\Hash;

class WithPassword implements SecretInterface
{
    public function key(): string
    {
        return 'password';
    }

    /**
     * @param string|null $value
     *
     * @return string|null
     */
    public function create(RegistrationData $data)
    {
        $data->password = Hash::make($data->password);
    }
}
