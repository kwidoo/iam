<?php

namespace App\Resolvers;

use App\Contracts\Resolver;
use App\Contracts\UserResolver as UserResolverContract;
use Illuminate\Contracts\Auth\Authenticatable;

class UserResolver implements UserResolverContract
{
    /**
     * @var Resolver[]
     */
    protected array $resolvers;

    public function __construct(array $resolvers)
    {
        $this->resolvers = $resolvers;
    }

    /**
     * @param string $identifier
     *
     * @return Authenticatable|null
     */
    public function resolve(string $identifier): ?Authenticatable
    {
        foreach ($this->resolvers as $resolver) {
            $resolved = (new $resolver)->resolve($identifier);
            if ($resolved instanceof Authenticatable) {
                return $resolved;
            }
        }
        return null;
    }
}
