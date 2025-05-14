<?php

namespace App\Data\Roles;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapOutputName(SnakeCaseMapper::class)]
class RoleCreateData extends Data
{
    public function __construct(
        #[Required, StringType, Unique('roles', 'name')]
        public string $name,

        #[Required, StringType]
        #[MapInputName('organization_id')]
        public string $organizationId,

        #[Required, StringType]
        #[MapInputName('guard_name')]
        public string $guardName,

        #[Required, StringType]
        public string $description,
    ) {}
}
