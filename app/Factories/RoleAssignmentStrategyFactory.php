<?php

namespace App\Factories;

use App\Contracts\Access\RoleAssignmentStrategy;
use InvalidArgumentException;

class RoleAssignmentStrategyFactory
{
    protected array $strategies = [];

    public function __construct(iterable $strategies)
    {
        foreach ($strategies as $key => $strategy) {
            $this->strategies[$key] = $strategy;
        }
    }

    public function make(string $key): RoleAssignmentStrategy
    {
        if (!isset($this->strategies[$key])) {
            throw new InvalidArgumentException("RoleAssignmentStrategy [$key] not found.");
        }

        return $this->strategies[$key];
    }
}
