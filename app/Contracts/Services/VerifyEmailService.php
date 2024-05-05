<?php

namespace App\Contracts;

use App\Models\Email;

interface VerifyEmailService
{
    public function __invoke(Email $email);
}
