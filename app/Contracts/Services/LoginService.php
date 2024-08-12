<?php

namespace App\Contracts\Services;

use App\Models\User;

interface LoginService
{
    /**
     * @param array<string,string> $data
     *
     * @return void
     */
    public function login(User $user, array $data): void;

    /**
     * @param User|null $user
     * @param array<string,string> $data
     *
     * @return void
     */
    public function failed(?User $user, array $data): void;
}
