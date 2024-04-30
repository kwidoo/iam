<?php

namespace App\Contracts;

interface CreateUserService
{
    public function __invoke(array $data): void;
}
