<?php

namespace App\Contracts\Services;

interface CreateUserService
{
    public function __invoke(array $data): void;
}
