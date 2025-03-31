<?php

namespace App\Strategies;

use App\Contracts\Services\PasswordStrategy;
use Exception;
use Illuminate\Support\Facades\Hash;

class WithPassword implements PasswordStrategy
{
    /**
     * @param string|null $value
     *
     * @return string|null
     */
    public function password(?string $value = null): ?string
    {
        if (is_null($value)) {
            throw new Exception('Password cannot be null');
        }
        return Hash::make($value);
    }

    public function method(): string
    {
        return 'password';
    }
}
