<?php

namespace App\Resolvers;

use App\Models\User;
use Kwidoo\Contacts\Contracts\ContactRepository;

class UserResolver
{
    public function __construct(
        protected ContactRepository $repository,
    ) {}

    public function resolve($identity, $value): ?User
    {
        return $this->repository
            ->where('value', $value)
            ->where('type', $identity)
            ->first()
            ?->contactable;
    }
}
