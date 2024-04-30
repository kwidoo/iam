<?php

namespace App\Contracts;

use App\Models\User;

interface LoginUser
{
    public static function retrieve(string $uuid): static;
    public function persist(): static;

    public function userLoggedIn(User $user, array $data): self;
    public function userLoginFailed(User $user, array $data): self;
}
