<?php

namespace App\Aggregates;

use App\Contracts\Aggregates\MicroServiceAggregate as MicroServiceAggregateContract;
use App\Data\Create\MicroServiceData;
use App\Events\MicroService\MicroServiceCreated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class MicroServiceAggregate extends AggregateRoot  implements MicroServiceAggregateContract
{
    /**
     * @param MicroServiceData $data
     *
     * @return self
     */
    public function create(MicroServiceData $data): self
    {
        $this->recordThat(new MicroServiceCreated($data));

        return $this;
    }
}
