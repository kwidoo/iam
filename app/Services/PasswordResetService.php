<?php

namespace App\Services;

use App\Aggregates\PasswordAggregateRoot;
use Kwidoo\Contacts\Contracts\Contact;
use Kwidoo\Contacts\Contracts\VerificationService as VerificationServiceContract;
use Kwidoo\Contacts\Contracts\Verifier;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

/**
 * @property \App\Aggregates\PasswordAggregateRoot aggregate
 **/
class PasswordResetService implements VerificationServiceContract
{
    public function __construct(
        protected Verifier $verifier,
        protected Contact $contact,
        protected AggregateRoot $aggregate
    ) {}

    /**
     * @param string|null $token
     *
     * @return void
     */
    public function create(): void
    {
        $this->aggregate->retrieve($this->contact->getKey())
            ->startPasswordChange($this->contact->getKey(), get_class($this->verifier))
            ->persist();

        // to change template, see UserProvider.php
        $this->verifier->create($this->contact);
    }

    /**
     * @param string $token
     *
     * @return bool
     */
    public function verify(string $token): bool
    {
        $verified = $this->verifier->verify($this->contact, $token);

        if ($verified) {
            $this->markVerified();
        }

        return $verified;
    }

    /**
     * @return void
     */
    public function markVerified(): void
    {
        $this->aggregate->retrieve($this->contact->getKey())
            ->passwordChanged($this->contact->getKey(), get_class($this->verifier))
            ->persist();
    }
}
