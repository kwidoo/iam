<?php

namespace App\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface UserResolver
{
    /**
     * @param string $identifier
     *
     * @return Authenticatable|null
     */
    public function resolve(string $identifier): ?Authenticatable;
}
