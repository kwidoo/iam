<?php

namespace App\Contracts\Services;

interface CreateUserService
{

    /**
     * @param array<string,string> $data
     *
     * @return void
     */
    public function __invoke(array $data): void;
}
