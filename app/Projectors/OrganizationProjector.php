<?php

namespace App\Projectors;

use App\Events\Organization\OrganizationCreated;
use App\Models\Organization;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class OrganizationProjector extends Projector
{
    public function onOrganizationCreated(OrganizationCreated $event)
    {
        $organization = new Organization($event->data);
        $organization->writeable()->save();
    }
}
