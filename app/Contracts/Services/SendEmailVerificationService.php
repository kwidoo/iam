<?php

namespace App\Contracts\Services;

use App\Models\Email;

interface SendEmailVerificationService
{
    public function __invoke(Email $email): void;
}
