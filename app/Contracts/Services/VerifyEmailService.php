<?php

namespace App\Contracts\Services;

use App\Models\Email;

interface VerifyEmailService
{
    public function __invoke(Email $email): void;
}
