<?php

namespace App\Projectors;

use App\Events\MicroService\MicroServiceCreated;
use App\Models\MicroService;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class MicroServiceProjector extends Projector
{
    /**
     * @param MicroServiceCreated $event
     *
     * @return void
     */
    public function onMicroServiceCreated(MicroServiceCreated $event): void
    {
        $microServiceData = $event->data;
        $microService = MicroService::where('client_id', $microServiceData->clientId)
            ->firstOrNew([
                'name' => $microServiceData->name,
                'client_id' => $microServiceData->clientId,
            ]);
        $microService->writeable()->save();
    }
}
