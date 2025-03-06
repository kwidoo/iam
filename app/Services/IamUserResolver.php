<?php

namespace App\Services;

use Kwidoo\Contacts\Models\Contact;
use Kwidoo\MultiAuth\Contracts\UserResolver;
use Laravel\Passport\Bridge\User;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use RuntimeException;

class IamUserResolver implements UserResolver
{
    public function resolve(string $username, ?ClientEntityInterface $clientEntity = null, string $authMethod = 'password'): ?User
    {
        $provider = $clientEntity->provider ?: config('auth.guards.api.provider');
        $model = config('auth.providers.' . $provider . '.model');

        if (is_null($model)) {
            throw new RuntimeException('Unable to determine authentication model from configuration.');
        }

        $contactModel = config('contacts.models.contact');
        if (!$contactModel) {
            throw new RuntimeException('Unable to determine contact model from configuration.');
        }

        $user = null;
        $contact = $contactModel::where('value', ltrim($username, '+'))->where('is_primary', 1)->first();

        if ($authMethod !== 'password') {
            if (!$contact) {
                $user = $model::create([
                    'password' => null,
                ]);
                $contact = $user->contacts()->create([
                    'value' => ltrim($username, '+'),
                    'type' => $authMethod,
                    'is_primary' => 1,
                    'is_verified' => 1,
                ]);
            }
        }

        $user = $contact->contactable;

        return $user ? new User($user->getAuthIdentifier()) : null;
    }
}
