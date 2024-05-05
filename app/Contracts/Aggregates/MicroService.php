<?php

namespace App\Contracts\Aggregates;

interface MicroServiceAggregate extends Aggregate
{
    public function create(array $data): self;
}
