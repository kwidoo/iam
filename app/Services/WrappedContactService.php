<?php

namespace App\Services;

use Kwidoo\Contacts\Contracts\ContactService;
use Exception;
use Kwidoo\Contacts\Contracts\Contact;
use Kwidoo\Mere\Contracts\Lifecycle;

class WrappedContactService implements ContactService
{
    public function __construct(
        protected ContactService $delegate,
        protected Lifecycle $lifecycle,
    ) {}

    public function create(string $type, string $value): string|int
    {
        $this->lifecycle
            ->run(
                action: 'createIdentity',
                resource: 'identity',
                context: [
                    'type' => $type,
                    'value' => $value,
                ],
                callback: fn() => $this->delegate->create($type, $value)
            );

        return '';
    }

    public function destroy(Contact $contact): bool
    {
        throw new Exception('Not implemented');
    }

    public function restore(string $uuid): bool
    {
        throw new Exception('Not implemented');
    }
}
