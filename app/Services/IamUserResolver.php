<?php

namespace App\Services;

use Kwidoo\Contacts\Contracts\ContactService;
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

        $contactModel = config('contacts.model');
        if (!$contactModel) {
            throw new RuntimeException('Unable to determine contact model from configuration.');
        }

        /** @var \App\Models\User */
        $user = null;
        $contact = $contactModel::where('value', ltrim($username, '+'))->where('is_primary', 1)->first();

        if ($authMethod !== 'password') {
            if (!$contact) {
                /** @var \App\Models\User */
                $user = $model::create([
                    'password' => null,
                ]);
                $contactService = app()->make(ContactService::class, ['model' => $user]);
                $uuid = $contactService->create($authMethod, ltrim($username, '+'));

                $contact = $contactModel::find($uuid);
            }
        }

        /** @var \App\Models\User */
        $user = $contact->contactable;

        return $user ? new User($user->getAuthIdentifier()) : null;
    }
}
