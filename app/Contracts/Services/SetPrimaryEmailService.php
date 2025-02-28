<?php

namespace App\Contracts\Services;

use App\Models\Email;

interface SetPrimaryEmailService
{
    /**
     * @param Email $email
     * @param string $referenceId
     *
     * @return void
     */
    public function __invoke(Email $email, string $referenceId): void;
}
