<?php

namespace App\Projectors;

use App\Events\User\UserCreated;
use App\Events\User\UserLoggedIn;
use App\Events\User\UserLoginFailed;
use App\Models\Email;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class UserProjector extends Projector
{
    /**
     * @param UserCreated $event
     *
     * @return void
     */
    public function onUserCreated(UserCreated $event): void
    {
        $user = (new User([
            'uuid' => $event->data['uuid'],
            'password' => $event->data['password'],
        ]));
        $user->save();
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

    /**
     * @return void
     */
    public function resetState(): void
    {
        Model::unguard();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');


        try {
            Email::truncate();
            User::truncate();
        } catch (\Exception $e) {
            throw $e;
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            Model::reguard();
        }
    }
}
