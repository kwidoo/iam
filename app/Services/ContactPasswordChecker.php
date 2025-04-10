<?php

namespace App\Services;

use Kwidoo\Contacts\Contracts\ContactRepository;
use Kwidoo\MultiAuth\Contracts\PasswordCheckerInterface;

/**
 * Custom implementation for checking passwords in the application-specific structure
 * where user and contact information are in separate tables.
 *
 * @todo Refactor here when 2FA will be required
 */
class ContactPasswordChecker implements PasswordCheckerInterface
{
    public function __construct(protected ContactRepository $repository) {}
    /**
     * Check if a user has a password set by first finding their ID through the contacts model
     * and then checking the users table.
     *
     * @param string $username The username/email from the contacts table
     * @return bool
     */
    public function hasPassword(string $username): bool
    {
        $contact = $this->repository->findByField('value', $username)->first();
        if (!$contact || !$contact->contactable) {
            return false;
        }

        return $contact->contactable->password !== null;
    }
}
