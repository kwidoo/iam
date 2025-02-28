<?php

namespace App\Services;

use App\Contracts\Services\CreateMicroService as CreateMicroServiceContract;
use App\Contracts\Aggregates\MicroServiceAggregate;
use App\Data\Create\MicroServiceData;

class CreateMicroService implements CreateMicroServiceContract
{
    public function __construct(protected MicroServiceAggregate $aggregate)
    {
        //
    }

    public function create(MicroServiceData $data): void
    {
        $this->aggregate->retrieve(
            $data->clientId
        )->create($data)->persist();
    }
}
