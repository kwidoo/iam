<?php

namespace App\Http\Controllers\Traits;

trait PhoneChallenge
{
    /**
     * @param array<string,mixed> $validated
     *
     * @return array<string,mixed>
     */
    protected function makePhoneChallenge(array $validated): array
    {
        $validated['password'] = rand(100000, 999999); // @todo make possible to set numbers
        cache()->put('phone_validation_' . $validated['full_phone'], $validated, 3600);
        return $validated;
    }
}
