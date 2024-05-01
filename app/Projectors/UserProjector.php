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
        $user = new User($event->data);
        $user->save();
    }

    /**
     * @param AfterUserCreated $event
     *
     * @return void
     */
    public function onAfterUserCreated(AfterUserCreated $event): void
    {
        $userProfile = new UserProfile();
        $userProfile->uuid = $event->data['user_uuid'];
        $userProfile->email = $event->data['email'];
        $userProfile->email_verified_at = null;
        $userProfile->password = $event->data['name'];
        $userProfile->organization_uuid = $event->data['organization_uuid'];
        $userProfile->organization_name = $event->data['organization_name'];
        $userProfile->profile_uuid = $event->data['profile_uuid'];
        $userProfile->profile_name = $event->data['profile_name'];

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
