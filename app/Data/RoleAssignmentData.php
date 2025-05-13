<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class RoleAssignmentData extends Data
{
    public function __construct(
        #[Required()]
        #[MapInputName('role_id')]
        public string $roleId,

        #[Required()]
        #[MapInputName('user_id')]
        public string $userId,

        #[Required()]
        #[MapInputName('organization_id')]
        public string $organizationId,
    ) {}
}
