<?php

namespace App\Projectors;

use App\Events\MicroService\MicroServiceCreated;
use App\Models\MicroService;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class MicroServiceProjector extends Projector
{
    public function onMicroServiceCreated(MicroServiceCreated $event)
    {
        $microService = MicroService::where('client_id', $event->data['client_id'])
            ->firstOrNew([
                'name' => $event->data['name'],
                'client_id' => $event->data['client_id']
            ]);
        $microService->writeable()->save();
    }
}
