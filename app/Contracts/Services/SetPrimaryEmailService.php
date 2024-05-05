<?php

namespace App\Contracts;

use App\Models\Email;

interface SetPrimaryEmailService
{
    public function __invoke(Email $email);
}
