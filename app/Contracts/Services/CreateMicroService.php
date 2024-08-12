<?php

namespace App\Contracts\Services;

use App\Data\Create\MicroServiceData;

interface CreateMicroService
{
    /**
     * @param MicroServiceData $data
     *
     * @return void
     */
    public function create(MicroServiceData $data): void;
}
