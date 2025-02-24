<?php

namespace App\Contracts\Services;

interface TwilioService
{
    public function createVerification(string $phoneNumber): void;
    public function sanitizePhoneNumber(string $phoneNumber): string;
    public function verify(string $phoneNumber, string $code);
}
