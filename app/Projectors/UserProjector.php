<?php

namespace App\Projectors;

use App\Events\User\UserCreated;
use App\Models\Email;
use App\Models\User;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class UserProjector extends Projector
{
    public function onUserCreated(UserCreated $event)
    {
        $password = request()->input('password');
        $user = (new User([
            'uuid' => $event->data['uuid'],
            'password' => $password,
        ]));
        $user->save();

        $data = [
            'email' => request()->input('email'),
            'user_uuid' => $user->uuid,
            'is_primary' => false,
        ];


        Email::createEmail($data);
    }
}
