<?php

namespace App\Data\Organizations;

use App\Enums\OrganizationFlow;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapOutputName(SnakeCaseMapper::class)]
class OrganizationCreateData extends Data
{
    public function __construct(
        #[Required, StringType]
        public string $name,

        #[Required, StringType]
        #[MapInputName('slug'), Unique('organizations', 'slug')]
        public string $slug,

        #[Required, StringType]
        #[MapInputName('owner_id')]
        public string $ownerId,

        public ?OrganizationFlow $flow = null,
    ) {
        //
    }

    public function user() //: string
    {
        //return app()->make(UserRepository::class)->find($this->ownerId);
    }
}
