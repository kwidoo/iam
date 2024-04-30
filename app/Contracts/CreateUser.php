<?php

namespace App\Contracts;

interface CreateUser
{
    public static function retrieve(string $uuid): static;
    public function persist(): static;

    public function createUser(array $data): self;
    public function updateUserAfterCreated(array $data): self;
}
