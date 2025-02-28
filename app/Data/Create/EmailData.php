<?php

namespace App\Data\Create;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapInputName;

class EmailData extends Data
{
    /**
     * @var bool
     */
    public bool $isPrimary = false;

    public function __construct(
        #[MapInputName('email_uuid')]
        public string $uuid,
        public string $email,
        #[MapInputName('user_uuid')]
        public string $userUuid,
        #[MapInputName('reference_id')]
        public string $referenceId,

    ) {
    }
}
