<?php

namespace App\Strategies\Password;

use App\Contracts\Services\Strategy;
use App\Data\RegistrationData;
use Illuminate\Support\Facades\Hash;

class WithPassword implements Strategy
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
