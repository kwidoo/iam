<?php

namespace App\Contracts\Services;

use App\Enums\OrganizationFlow;
use Kwidoo\Mere\Contracts\Data\RegistrationData;
use Spatie\LaravelData\Contracts\BaseData;

interface OrganizationCreateService
{
    public function key(): OrganizationFlow|string;

    /**
     * @param \App\Data\Registration\DefaultRegistrationData $data
     *
     * @return \Kwidoo\Mere\Contracts\Models\OrganizationInterface;
     */
    public function create(BaseData $data);
}
