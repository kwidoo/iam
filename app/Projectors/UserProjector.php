<?php

namespace App\Projectors;

use App\Events\User\UserCreated;
use App\Events\UserLoggedIn;
use App\Events\UserLoginFailed;
use App\Models\Email;
use App\Models\User;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class UserProjector extends Projector
{
    /**
     * @param UserCreated $event
     *
     * @return [type]
     */
    public function onUserCreated(UserCreated $event): void
    {
        $user = (new User(
            [
                'uuid' => $event->data['uuid'],
                'password' => $event->data['password'],
            ]
        ));
        $user->save();

        $data = [
            'email' => $event->data['email'],
            'user_uuid' => $user->uuid,
            'is_primary' => false,
        ];


        Email::createEmail($data);
    }

    /**
     * @param UserLoggedIn $event
     *
     * @return void
     */
    public function onUserLoggedIn(UserLoggedIn $event): void
    {
        //
    }

    /**
     * @param UserLoginFailed $event
     *
     * @return void
     */
    public function onUserLoginFailed(UserLoginFailed $event): void
    {
        // @todo add fail 2 ban logic
    }
}
