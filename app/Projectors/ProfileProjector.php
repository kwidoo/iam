<?php

namespace App\Projectors;

use App\Events\Profile\ProfileCreated;
use App\Models\Profile;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ProfileProjector extends Projector
{
    public function onProfileCreated(ProfileCreated $event)
    {
        $profileData = $event->data;
        $profile = new Profile([
            'uuid' => $profileData->uuid,
            'name' => $profileData->name,
            'user_uuid' => $profileData->userUuid,
            'organization_uuid' => $profileData->organizationUuid,
        ]);
        $profile->writeable()->save();
    }
}
