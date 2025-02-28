<?php

namespace App\Services;

use App\Contracts\Aggregates\UserAggregate;
use App\Contracts\Services\LoginService as LoginServiceContract;
use App\Models\User;

class LoginService implements LoginServiceContract
{
    public function __construct(protected UserAggregate $aggregate)
    {
        //
    }
    /**
     * @param array<string,string> $data
     *
     * @return void
     */
    public function login(User $user, array $data): void
    {
        $this->aggregate
            ->userLoggedIn($user, $data)
            ->persist();
    }

    /**
     * @param User|null $user
     * @param array<string,string> $data
     *
     * @return void
     */
    public function failed(?User $user, array $data): void
    {
        $this->aggregate
            ->userLoginFailed($user, $data)
            ->persist();
    }
}
