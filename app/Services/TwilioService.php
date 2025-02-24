<?php

namespace App\Services;

use Twilio\Rest\Client;
use App\Contracts\Services\TwilioService as TwilioServiceContract;
use Exception;
use Illuminate\Support\Facades\Cache;

class TwilioService implements TwilioServiceContract
{

    public function __construct(protected Client $client) {}

    /**
     * @param string $phoneNumber
     *
     * @return void
     */
    public function createVerification(string $phoneNumber): void
    {
        $sanitizedPhoneNumber = $this->sanitizePhoneNumber($phoneNumber);

        $this->client->verify->v2->services(config('twilio.verify_sid'))
            ->verifications
            ->create($sanitizedPhoneNumber, 'sms');
    }


    public function verify(string $phoneNumber, string $code): bool
    {
        $sanitizedPhoneNumber = $this->sanitizePhoneNumber($phoneNumber);

        $verification = $this->client
            ->verify
            ->v2
            ->services(config('twilio.verify_sid'))
            ->verificationChecks
            ->create([
                'to' => $sanitizedPhoneNumber,
                'code' => $code
            ]);

        if (!$verification->valid) {
            throw new Exception('Invalid verification code');
        }

        return true;
    }

    /**
     * Sanitize the phone number by ensuring only numbers and '+' are present.
     * If the phone number doesn't start with '+', add it.
     *
     * @param string $phoneNumber
     *
     * @return string
     */
    public function sanitizePhoneNumber(string $phoneNumber): string
    {
        $phoneNumber = preg_replace('/[^0-9+]/', '', $phoneNumber);

        if (strpos($phoneNumber, '+') !== 0) {
            $phoneNumber = '+' . $phoneNumber;
        }

        return $phoneNumber;
    }
}
