<?php

namespace App\Contracts;

use App\Models\Email;

interface CreateEmail
{
    public static function retrieve(string $uuid): static;
    public function persist(): static;

    public function createEmail(array $data): self;
    public function unsetPrimaryEmail(Email $email, string $referenceId): self;
    public function setPrimaryEmail(Email $email, string $referenceId): self;
    public function removeEmail(Email $email, string $referenceId): self;
    public function updateUserAfterEmailCreated(array $data): self;
    public function verifyEmail(Email $email, string $referenceId): self;
    public function sendEmailVerification(Email $email, string $referenceId): self;
}
