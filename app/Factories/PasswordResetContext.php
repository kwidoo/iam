<?php

namespace App\Factories;

use Kwidoo\Contacts\Contracts\Contact;
use Kwidoo\Contacts\Contracts\VerificationContext;

class PasswordResetContext implements VerificationContext
{
    public function getTemplate(Contact $contact): string
    {
        if ($contact->type === 'phone') {
            return 'phone_verification';
        }
        return 'TokenNotification';
    }
}
