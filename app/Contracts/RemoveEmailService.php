<?php

namespace App\Contracts;

use App\Models\Email;

interface RemoveEmailService
{
    public function __invoke(Email $email);
}
