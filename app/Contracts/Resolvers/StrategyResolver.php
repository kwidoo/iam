<?php

namespace App\Contracts\Resolvers;

interface StrategyResolver
{
    public function resolve(string $type, mixed $service): object;
}
