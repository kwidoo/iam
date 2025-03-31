<?php

namespace Tests\Feature;

use Kwidoo\Contacts\Contracts\Contact;
use Kwidoo\Contacts\Contracts\Verifier;

class FakeVerifier implements Verifier
{
    public function create(Contact $contact): void
    {
        // Simulate sending email
        $contact->metadata = ['sent_via' => 'fake_email'];
    }

    public function verify(Contact $contact, string $token): bool
    {
        return true;
    }
}
