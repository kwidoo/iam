<?php

namespace App\Services;

use App\Models\Phone;
use Kwidoo\MultiAuth\Contracts\UserResolver;
use Laravel\Passport\Bridge\User;
use League\OAuth2\Server\Entities\ClientEntityInterface;

class IamUserResolver implements UserResolver
{
    public function resolve(string $username, ?ClientEntityInterface $clientEntity = null): ?User
    {
        $user = null;

        $phone = Phone::where('full_phone', ltrim($username, '+'))->where('is_primary', 1)->firstOrFail();
        if ($phone) {
            $user = $phone->user;
        }
        return $user ? new User($user->getAuthIdentifier()) : null;
    }
}
