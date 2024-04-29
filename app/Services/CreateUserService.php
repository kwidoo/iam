<?php

namespace App\Services;

use App\Aggregates\UserAggregate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateUserService
{
    /**
     * @param array $data
     *
     * @return void
     */
    public static function createUser(array $data)
    {
        try {
            DB::transaction(function () use ($data) {
                $data['email_uuid'] = Str::uuid()->toString();
                $data['organization_uuid'] = Str::uuid()->toString();
                $data['organization_name'] = $data['organization_name'] ?? 'default';
                $data['profile_uuid'] = Str::uuid()->toString();
                $data['profile_name'] = $data['profile_name'] ?? 'default';

                (new UserAggregate)->retrieve($data['user_uuid'])
                    //
                    ->createUser($data)
                    //
                    ->createEmail($data)
                    //
                    ->createOrganization($data)
                    //
                    ->createProfile($data)
                    //
                    ->updateUserAfterCreate($data)
                    //
                    ->persist($data['reference_id']);
            });
        } catch (\Exception $e) {
            throw $e;
            abort(422, 'User creation failed');
        }
    }
}
