<?php

namespace App\Services;

use App\Contracts\CreateMicroService as CreateMicroServiceContract;
use App\Contracts\MicroServiceAggregate;

class CreateMicroService implements CreateMicroServiceContract
{
    public function __construct(protected MicroServiceAggregate $aggregate)
    {
        //
    }

    public function create(array $data): void
    {
        $this->aggregate->retrieve(
            $data['client_id']
        )->create([
            'uuid' => $data['service_uuid'],
            'name' => $data['name'],
            'client_id' => $data['client_id'],
        ])->persist($data['reference_id']);
    }
}
