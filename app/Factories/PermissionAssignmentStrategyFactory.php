<?php

namespace App\Factories;

use App\Contracts\Access\PermissionAssignmentStrategy;
use InvalidArgumentException;
use Kwidoo\Mere\Contracts\Lifecycle;

class PermissionAssignmentStrategyFactory
{
    protected array $strategies = [];

    public function __construct(iterable $strategies)
    {
        foreach ($strategies as $key => $strategy) {
            $this->strategies[$key] = $strategy;
        }
    }

    public function make(string $key, Lifecycle $lifecycle): PermissionAssignmentStrategy
    {
        if (!isset($this->strategies[$key])) {
            throw new InvalidArgumentException("RoleAssignmentStrategy [$key] not found.");
        }

        return app()->make($this->strategies[$key], ['lifecycle' => $lifecycle]);
    }
}
