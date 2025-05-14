<?php

namespace App\Contracts\Services;

use App\Enums\RegistrationFlow;
use Kwidoo\Mere\Contracts\Data\RegistrationData;

interface OrganizationCreateService
{
    public function key(): RegistrationFlow|string;

    /**
     * @param \App\Data\Registration\DefaultRegistrationData $data
     *
     * @return \Kwidoo\Mere\Contracts\Models\OrganizationInterface;
     */
    public function create(RegistrationData $data);
}
