<?php

namespace App\Aggregates;

use App\Contracts\Aggregates\MicroServiceAggregate as MicroServiceAggregateContract;
use App\Events\MicroService\MicroServiceCreated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class MicroServiceAggregate extends AggregateRoot // implements MicroServiceAggregateContract
{
    public function create(array $data): self
    {
        $this->recordThat(new MicroServiceCreated([
            'uuid' => $data['uuid'],
            'name' => $data['name'],
            'client_id' => $data['client_id'],
        ]));

        return $this;
    }
}
