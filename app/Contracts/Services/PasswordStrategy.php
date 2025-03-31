<?php

namespace App\Contracts\Services;

interface PasswordStrategy
{
    public function password(?string $value = null): ?string;

    public function method(): string;
}
