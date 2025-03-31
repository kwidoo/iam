<?php

namespace App\Factories;

use App\Contracts\Services\PasswordStrategy;
use App\Exceptions\UserCreationException;

class PasswordStrategyFactory
{
    /**
     * @param PasswordStrategy[] $strategies
     */
    public function __construct(protected iterable $strategies) {}

    public function resolve(string $method): PasswordStrategy
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->method() === $method) {
                return $strategy;
            }
        }

        throw new UserCreationException("Invalid registration method: $method");
    }
}
