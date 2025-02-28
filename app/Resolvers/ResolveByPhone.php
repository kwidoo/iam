<?php

namespace App\Resolvers;

use App\Contracts\Resolver;
use App\Models\Phone;
use Illuminate\Contracts\Auth\Authenticatable;

class ResolveByPhone implements Resolver
{
    public function resolve(string $identifier): ?Authenticatable
    {
        $phone = Phone::findByFullPhone($identifier);

        if (!$phone?->is_primary) {
            return null;
        }

        return $phone?->user;
    }
}
