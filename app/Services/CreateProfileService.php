<?php

namespace App\Services;

use App\Aggregates\StandardUserAggregate;
use App\Data\Create\ProfileData;
use Illuminate\Support\Facades\DB;
use Exception;

class CreateProfileService
{
    /**
     * @param ProfileData $profileData
     *
     * @return void
     */
    public static function createProfile(ProfileData $profileData): void
    {
        try {
            DB::transaction(function () use ($profileData) {

                (new StandardUserAggregate)->retrieve($profileData->userUuid)
                    ->createProfile($profileData)
                    ->persist();
            });
        } catch (Exception $e) {
            abort(422, 'Profile creation failed');
        }
    }
}
