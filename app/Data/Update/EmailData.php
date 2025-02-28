<?php

namespace App\Data\Update;

use App\Models\Email;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class EmailData extends Data
{
    /**
     * @var Email
     */
    public Email $email;

    public function __construct(
        public Email|string $emailValue,
        #[MapInputName('reference_id')]
        public string $referenceId,
    ) {
        $this->email = is_string($emailValue) ? Email::where('email', $emailValue)->firstOrFail() : $emailValue;
    }
}
