<?php

namespace App\Data\Create;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class PhoneData extends Data
{
    public function __construct(
        #[MapInputName('phone_uuid')]
        public string $uuid,
        #[MapInputName('country_code')]
        public string $countryCode,
        public string $phone,
        public string $password,
        #[MapInputName('user_uuid')]
        public string $userUuid,
        #[MapInputName('reference_id')]
        public string $referenceId,
    ) {
    }
}
