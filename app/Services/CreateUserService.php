<?php

namespace App\Services;

use App\Aggregates\UserAggregate;
use Illuminate\Support\Facades\DB;

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

                (new UserAggregate)->retrieve($data['uuid'])
                    ->createUser($data)
                    ->createEmail([...$data, 'user_uuid' => $data['uuid']])
                    ->persist($data['reference_id']);
            });
        } catch (\Exception $e) {
            throw $e;
            abort(422, 'User creation failed');
        }
    }
}
