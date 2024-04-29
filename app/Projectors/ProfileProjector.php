<?php

namespace App\Projectors;

use App\Events\Profile\ProfileCreated;
use App\Models\Profile;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ProfileProjector extends Projector
{
    public function onProfileCreated(ProfileCreated $event)
    {
        $profile = new Profile($event->data);
        $profile->writeable()->save();
    }
}
