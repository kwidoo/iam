<?php

namespace App\Strategies\Profile;

use App\Contracts\Services\ProfileService;
use App\Contracts\Services\Strategy;
use App\Data\RegistrationData;

class ProfileStrategy implements Strategy
{
    public function __construct(protected ProfileService $service) {}

    public function key(): string
    {
        return 'default_profile';
    }

    /**
     * @param RegistrationData $data
     *
     * @return RegistrationData
     */
    public function create(RegistrationData $data)
    {
        $data->profile = $this->service->registerProfile($data);
    }
}
