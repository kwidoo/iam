<?php

namespace App\Data\Create;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class ProfileData extends Data
{
    public function __construct(
        #[MapInputName('profile_uuid')]
        public string $uuid,
        #[MapInputName('profile_name')]
        public string $name,
        #[MapInputName('user_uuid')]
        public string $userUuid,
        #[MapInputName('organization_uuid')]
        public string $organizationUuid,
        #[MapInputName('reference_id')]
        public string $referenceId,
        public string $type = 'default',
    ) {
    }
}
