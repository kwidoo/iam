<?php

namespace App\Factories;

use App\Contracts\Access\PermissionAssignmentStrategy;
use InvalidArgumentException;

class PermissionAssignmentStrategyFactory
{
    protected array $strategies = [];

    public function __construct(iterable $strategies)
    {
        foreach ($strategies as $key => $strategy) {
            $this->strategies[$key] = $strategy;
        }
    }

    public function make(string $key): PermissionAssignmentStrategy
    {
        if (!isset($this->strategies[$key])) {
            throw new InvalidArgumentException("RoleAssignmentStrategy [$key] not found.");
        }

        return $this->strategies[$key];
    }
}
