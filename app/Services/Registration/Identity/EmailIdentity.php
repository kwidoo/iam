<?php

namespace App\Services\Registration\Identity;

use Kwidoo\Mere\Contracts\Data\RegistrationData;
use App\Services\Registration\IdentityInterface;
use Kwidoo\Contacts\Contracts\ContactService;
use Kwidoo\Contacts\Data\ContactCreateData;

class EmailIdentity implements IdentityInterface
{
    public function __construct(
        protected ContactService $service
    ) {}

    public function key(): string
    {
        return 'email';
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
                'method' => $this->key(),
                'value' => $data->value,
            ])
        );
    }
}
