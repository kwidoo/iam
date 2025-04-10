<?php

namespace App\Services;

use App\Models\User;
use Kwidoo\MultiAuth\Contracts\UserResolver;
use Laravel\Passport\Bridge\UserRepository as PassportUserRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use Illuminate\Contracts\Hashing\Hasher;

class UserRepository extends PassportUserRepository
{
    /**
     * Create a new repository instance.
     *
     * @param  \Illuminate\Contracts\Hashing\Hasher  $hasher
     * @return void
     */
    public function __construct(Hasher $hasher, protected UserResolver $userResolver)
    {
        parent::__construct($hasher);
    }


    public function getUserEntityByUserCredentials($username, $passwordOrCode, $grantType, ClientEntityInterface $clientEntity)
    {
        $resolvedUser = app()->make(UserResolver::class)->resolve($username, $clientEntity);

        $user = User::find($resolvedUser->getIdentifier());

        // IMPORTANT!!! Should not be able to login without password at this point
        if (!$this->hasher->check($passwordOrCode, $user->password) || $user->password === null) {
            return;
        }

        return $resolvedUser;
    }
}
