<?php

namespace App\Resolvers;

use App\Criteria\ByContact;
use App\Models\User;
use Kwidoo\Contacts\Contracts\ContactRepository;

class UserResolver
{
    public function __construct(
        protected ContactRepository $repository,
    ) {}

    /**
     * @param mixed $identity
     * @param mixed $value
     *
     * @return User|null
     */
    public function resolve(string $identity, string $value): ?User
    {
        return $this->repository
            ->pushCriteria(new ByContact($value, $identity))
            ->first()
            ?->contactable;
    }
}
