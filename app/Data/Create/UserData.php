<?php

namespace App\Data\Create;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class UserData extends Data
{
    public function __construct(
        #[MapInputName('user_uuid')]
        public string $uuid,
        #[MapInputName('reference_id')]
        public string $referenceId,
        public ?string $password = null,

    ) {
        if (!$password) {
            $this->password = bcrypt(bin2hex(random_bytes(16)));
        }
    }
}
