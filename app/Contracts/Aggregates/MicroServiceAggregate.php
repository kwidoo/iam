<?php

namespace App\Contracts\Aggregates;

use App\Data\Create\MicroServiceData;

interface MicroServiceAggregate extends Aggregate
{
    /**
     * @param MicroServiceData $data
     *
     * @return self
     */
    public function create(MicroServiceData $data): self;
}
