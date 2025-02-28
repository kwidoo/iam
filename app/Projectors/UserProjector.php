<?php

namespace App\Projectors;

use App\Contracts\Models\UserReadModel;
use App\Contracts\Models\UserWriteModel;
use App\Events\User\AfterUserCreated;
use App\Events\User\UserCreated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

/**
 *
 * @package App\Projectors
 *
 * @property \App\Models\User $userWriteModel
 * @property \App\Models\UserProfile $userReadModel
 */
class UserProjector extends Projector
{
    /**
     * @param UserCreated $event
     *
     * @return void
     */
    public function onUserCreated(UserCreated $event): void
    {
        $userData = $event->data;
        $withPassword = config('iam.password', true) ? ['password' => bcrypt($userData->password)] : [];

        app()->make(UserWriteModel::class)->create([
            'uuid' => $userData->uuid,
            ...$withPassword,
        ]);
    }

    /**
     * @param AfterUserCreated $event
     *
     * @return void
     */
    public function onAfterUserCreated(AfterUserCreated $event): void
    {
        $userData = $event->userData;
        $user = $userData->user;

        app()->make(UserReadModel::class)->fill([
            'uuid' => $userData->uuid,
            ...$userData->toArray(),
            'organizations' => $user?->organizations?->toArray(),
        ])->save();
    }
}
