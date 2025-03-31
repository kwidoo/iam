<?php

namespace App\Factories;

use App\Contracts\Services\RegisterStrategy;
use App\Exceptions\UserCreationException;

class RegisterStrategyResolver
{
    /**
     * @param RegisterStrategy[] $strategies
     */
    public function __construct(protected iterable $strategies) {}

    public function resolve(string $method): RegisterStrategy
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->method() === $method) {
                return $strategy;
            }
        }

        throw new UserCreationException("Invalid registration method: $method");
    }
}
