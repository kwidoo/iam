<?php

namespace App\Data\Create;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class MicroServiceData extends Data
{
    public function __construct(
        #[MapInputName('client_id')]
        public string $clientId,
        #[MapInputName('service_uuid')]
        public string $serviceUuid,
        public string $name,
    ) {
    }
}
