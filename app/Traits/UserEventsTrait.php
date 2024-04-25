<?php

namespace App\Traits;

use App\Events\User\UserCreated;
use App\Models\User;

trait UserEventsTrait
{
    public static function createUser(array $data): User
    {
        $data['uuid'] = (new self)->newUniqueId();
        event(new UserCreated($data));

        return static::find($data['uuid']);
    }
}
