<?php

namespace App\Traits;

use App\Events\User\UserCreated;
use App\Events\UserLoggedIn;
use App\Events\UserLoginFailed;
use App\Models\User;

trait UserEventsTrait
{
    /**
     * @param array $data
     *
     * @return User
     */
    public static function createUser(array $data): User
    {
        $data['uuid'] = (new self)->newUniqueId();
        event(new UserCreated($data));

        return static::find($data['uuid']);
    }

    /**
     * @return void
     */
    public function loginUser(): void
    {
        event(new UserLoggedIn($this));
    }

    /**
     * @return void
     */
    public function loginFailed(): void
    {
        event(new UserLoginFailed($this));
    }
}
