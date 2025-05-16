<?php

namespace App\Data\Profile;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class ProfileCreateData extends Data
{
    public function __construct(
        public string $fname,
        public string $lname,
        #[Required()]
        #[StringType]
        #[MapInputName('user_id')]
        public string $userId,
        public string $dob,
        public string $gender,
    ) {}
}
