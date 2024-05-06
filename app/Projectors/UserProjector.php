<?php

namespace App\Projectors;

use App\Events\User\AfterUserCreated;
use App\Events\User\UserCreated;
use App\Models\Email;
use App\Models\Organization;
use App\Models\Profile;
use App\Models\User;
use App\Models\UserProfile;
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
        $userData = $event->data;
        User::create([
            'uuid' => $userData->uuid,
            'password' => $userData->password,
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
        $user = User::findOrFail($userData->uuid);
        $userProfile = new UserProfile([
            ...$userData->toArray(),
            'organizations' => $user->organizations->toArray(),
        ]);

        $userProfile->save();
    }

    /**
     * @return void
     */
    public function resetState(): void
    {
        Model::unguard();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            UserProfile::truncate();
            Email::truncate();
            Profile::truncate();
            Organization::truncate();
            User::truncate();
        } catch (\Exception $e) {
            throw $e;
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            Model::reguard();
        }
    }
}
