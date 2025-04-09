<?php

namespace App\Services;

use App\Services\Traits\OnlyCreate;
use Kwidoo\Contacts\Contracts\ContactService as ContactServiceContract;
use Kwidoo\Mere\Contracts\Lifecycle;

class ContactService implements ContactServiceContract
{
    use OnlyCreate;

    public function __construct(
        protected ContactServiceContract $delegate,
        protected Lifecycle $lifecycle,
    ) {}

    public function create(string $type, string $value): string|int
    {
        $this->lifecycle
            ->run(
                action: 'createIdentity',
                resource: 'identity',
                context: $this->lifecycle->context(),
                callback: fn() => $this->delegate->create($type, $value)
            );

        return '';
    }
}
