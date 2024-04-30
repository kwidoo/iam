<?php

namespace App\Contracts;

use App\Models\Email;
use App\Models\User;

interface UserAggregate
{
    public static function retrieve(string $uuid): static;
    public function persist(): static;

    public function createUser(array $data): self;
    public function updateUserAfterCreated(array $data): self;

    public function createEmail(array $data): self;
    public function unsetPrimaryEmail(Email $email, string $referenceId): self;
    public function setPrimaryEmail(Email $email, string $referenceId): self;
    public function removeEmail(Email $email, string $referenceId): self;
    public function updateUserAfterEmailCreated(array $data): self;

    public function userLoggedIn(User $user, array $data): self;
    public function userLoginFailed(User $user, array $data): self;

    public function createProfile(array $data): self;
    public function createOrganization(array $data): self;
}
