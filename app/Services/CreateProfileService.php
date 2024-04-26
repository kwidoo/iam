<?php

namespace App\Services;

use App\Aggregates\UserAggregate;
use Illuminate\Support\Facades\DB;

class CreateProfileService
{
    /**
     * @param array $data
     *
     * @return void
     */
    public static function createProfile(array $data)
    {
        try {
            DB::transaction(function () use ($data) {

                (new UserAggregate)->retrieve($data['user_uuid'])
                    ->createProfile($data)
                    ->persist($data['reference_id']);
            });
        } catch (\Exception $e) {
            throw $e;
            abort(422, 'Profile creation failed');
        }
    }
}
