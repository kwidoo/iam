<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class OrganizationData extends Data
{
    public function __construct(
        #[MapInputName('organization_uuid')]
        public string $uuid,
        #[MapInputName('organization_name')]
        public string $name,
        #[MapInputName('user_uuid')]
        public string $userUuid,

    ) {
    }
}
