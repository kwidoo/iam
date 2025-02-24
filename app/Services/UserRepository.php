<?php

namespace App\Services;

use App\Contracts\UserResolver;
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
        $user = $this->userResolver->resolve($username);
        dump($username, $passwordOrCode, $grantType, $clientEntity);
        return parent::getUserEntityByUserCredentials($username, $passwordOrCode, $grantType, $clientEntity);
    }
}
