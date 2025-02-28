<?php

namespace App\Contracts\Services;

use App\Models\User;

interface AddEmailService
{
    public function __invoke(User $user, string $email, string $referenceId = null): void;
}
