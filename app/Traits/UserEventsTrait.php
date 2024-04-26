<?php

namespace App\Traits;

use App\Events\User\UserCreated;
use App\Events\User\UserLoggedIn;
use App\Events\User\UserLoginFailed;
use App\Models\User;
use Illuminate\Support\Str;

trait UserEventsTrait
{
    /**
     * @param array $data
     *
     * @return User
     */
    public static function createUser(array $data): User
    {
        if (!array_key_exists('uuid', $data)) {
            $data['uuid'] = (new self)->newUniqueId();
        }
        event(new UserCreated($data));

        return static::find($data['uuid']);
    }

    /**
     * @return void
     */
    public function loginUser($data): void
    {
        event(new UserLoggedIn($this, $data));
    }

    /**
     * @return void
     */
    public function loginFailed(): void
    {
        event(new UserLoginFailed($this));
    }
}
