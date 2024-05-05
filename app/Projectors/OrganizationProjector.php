<?php

namespace App\Projectors;

use App\Events\Organization\OrganizationCreated;
use App\Models\Organization;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class OrganizationProjector extends Projector
{
    public function onOrganizationCreated(OrganizationCreated $event)
    {
        $organizationData = $event->data;
        $organization = new Organization([
            'uuid' => $organizationData->uuid,
            'name' => $organizationData->name,
            'user_uuid' => $organizationData->userUuid,
        ]);
        $organization->writeable()->save();
    }
}
