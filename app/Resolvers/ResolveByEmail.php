<?php

namespace App\Resolvers;

use App\Contracts\Resolver;
use App\Models\Email;
use Illuminate\Contracts\Auth\Authenticatable;

class ResolveByEmail implements Resolver
{
    public function resolve(string $identifier): ?Authenticatable
    {
        $email = Email::findByEmail($identifier);

        if (!$email?->is_primary) {
            return null;
        }


        return $email?->user;
    }
}
