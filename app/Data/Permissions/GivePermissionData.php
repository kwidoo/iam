<?php

namespace App\Data\Permissions;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class GivePermissionData extends Data
{
    public function __construct(
        #[Required()]
        #[MapInputName('user_id')]
        public string $userId,
        #[Required()]
        #[MapInputName('permission_id')]
        public string $permissionId,
        #[Required()]
        #[MapInputName('organization_id')]
        public ?string $organizationId = null,
    ) {}
}
