<?php

namespace App\Contracts;

interface CreateMicroService
{
    public function create(array $data): void;
}
