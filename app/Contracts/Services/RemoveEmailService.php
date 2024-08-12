<?php

namespace App\Contracts\Services;

use App\Models\Email;

interface RemoveEmailService
{
    public function __invoke(Email $email, string $referenceId): void;
}
