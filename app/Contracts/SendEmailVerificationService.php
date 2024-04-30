<?php

namespace App\Contracts;

interface SendEmailVerificationService
{
    public function __invoke($email);
}
