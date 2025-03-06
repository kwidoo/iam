<?php

namespace App\Services;

use App\Aggregates\PasswordAggregateRoot;
use Kwidoo\Contacts\Aggregates\ContactAggregateRoot;
use Kwidoo\Contacts\Contracts\Contact;
use Kwidoo\Contacts\Contracts\VerificationService as VerificationServiceContract;
use Kwidoo\Contacts\Contracts\Verifier;

class PasswordResetService implements VerificationServiceContract
{
    public function __construct(
        protected Verifier $verifier,
        protected Contact $contact,
    ) {}

    /**
     * @param string|null $token
     *
     * @return void
     */
    public function create(): void
    {
        PasswordAggregateRoot::retrieve($this->contact->getKey())
            ->startPasswordChange($this->contact->getKey(), get_class($this->verifier))
            ->persist();

        $this->verifier->create();
    }

    /**
     * @param string $token
     *
     * @return bool
     */
    public function verify(string $token): bool
    {
        $verified = $this->verifier->verify($token);

        if ($verified) {
            PasswordAggregateRoot::retrieve($this->contact->getKey())
                ->passwordChanged($this->contact->getKey(), get_class($this->verifier))
                ->persist();
        }

        return $verified;
    }
}
