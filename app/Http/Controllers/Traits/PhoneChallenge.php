<?php

namespace App\Http\Controllers\Traits;

use App\Models\Phone;
use Illuminate\Support\Facades\Cache;
use Exception;

trait PhoneChallenge
{
    /**
     * @param array<string,mixed> $validated
     *
     * @return array<string,mixed>
     */
    protected function makePhoneChallenge(array $validated)
    {
        $currentTime = now();
        $sanitizedPhoneNumber = $this->twilioService->sanitizePhoneNumber($validated['full_phone']);

        $phone = Phone::findByFullPhone($validated['full_phone']);

        if ($phone && !$phone->is_primary) {
            throw new Exception();
        }

        /** @var int */
        $expire = Cache::get("verification: $sanitizedPhoneNumber") ?? null;

        if ($expire && $expire['at'] > $currentTime->timestamp) {
            $remainingTime = $expire['at'] - $currentTime->timestamp;
            return response()->json(['error' => 'Verification code already sent. Please wait for ' . $remainingTime . ' seconds.'], 422);
        }

        try {
            $this->twilioService->createVerification($sanitizedPhoneNumber);

            Cache::put("verification: $sanitizedPhoneNumber", [
                'at' => $currentTime->addMinute()->timestamp,
                'name' => $validated['name'],
                'organization_name' => $validated['organization_name'] ?? 'default',
                'organization_uuid' => $validated['organization_uuid'] ?? null,

            ]);

            return response()->json(['message' => 'Now you have to enter the received code in a text message'], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Verification code could not be sent'], 422);
        }
    }
}
