<?php

namespace App\Projectors;

use App\Events\Phone\PhoneCreated;
use App\Models\Phone;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class PhoneProjector extends Projector
{
    /**
     * @param PhoneCreated $event
     *
     * @return void
     */
    public function onEmailCreated(PhoneCreated $event): void
    {
        $phoneData = $event->data;
        $phone = (new Phone([
            'uuid' => $phoneData->uuid,
            'user_uuid' => $phoneData->userUuid,
            'country_code' => $phoneData->countryCode,
            'phone' => $phoneData->phone,
        ]));
        $phone->writeable()->save();
    }
}
