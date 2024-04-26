<?php

namespace App\Projectors;

use App\Data\ProfileData;
use App\Events\Profile\ProfileCreated;
use App\Models\Profile;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use Illuminate\Support\Str;

class ProfileProjector extends Projector
{
    public function onProfileCreated(ProfileCreated $event)
    {
        $uuid = Str::uuid();

        $profile = new Profile([
            'uuid' => $uuid->toString(),
            'user_uuid' => $event->data['user_uuid'],
            'type' => $event->data['type'],
            'data' => ProfileData::from([
                'uuid' => $uuid->toString(),
                'name' => $event->data['name'],
            ]),
        ]);

        $profile->writeable()->save();
    }
}
