<?php

namespace App\Services;

use App\Aggregates\UserAggregate;
use App\Models\User;

class LoginService
{
    /**
     * @param array $data
     *
     * @return void
     */
    public static function login(User $user, array $data)
    {
        $aggregate = (new UserAggregate)->retrieve($user->uuid);
        $aggregate
            ->userLoggedIn($user, $data)
            ->persist($data['reference_id']);
    }

    public static function failed(?User $user, array $data)
    {
        if ($user) {
            $aggregate = (new UserAggregate)->retrieve($user->uuid);
            $aggregate->userLoginFailed($user, $data);
        }
        // else {
        //     event(new UserLoginFailed(null, ['reference' => $data]));
        // }
    }
}
