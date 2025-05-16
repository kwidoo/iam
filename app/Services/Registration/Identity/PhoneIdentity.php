<?php

namespace App\Services\Registration\Identity;

use Kwidoo\Contacts\Contracts\ContactService;
use Kwidoo\Mere\Contracts\Data\RegistrationData;
use App\Services\Registration\IdentityInterface;
use Kwidoo\Contacts\Data\ContactCreateData;

class PhoneIdentity implements IdentityInterface

{
    public function __construct(protected ContactService $service) {}

    public function key(): string
    {
        return 'phone';
    }

    /**
     * @param \App\Data\Registration\DefaultRegistrationData $data
     *
     * @return [type]
     */
    public function create(RegistrationData $data)
    {
        return $this->service->create(
            ContactCreateData::from([
                'method' => 'phone',
                'value' => $data->value,
            ])
        );
    }
}
