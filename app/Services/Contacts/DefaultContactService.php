<?php

namespace App\Services;

use Kwidoo\Mere\Services\Traits\OnlyCreate;
use Kwidoo\Lifecycle\Traits\RunsLifecycle;

use Kwidoo\Contacts\Contracts\ContactService;
use Kwidoo\Contacts\Data\ContactCreateData;
use Kwidoo\Contacts\Services\ContactService as CoreContactService;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;

class DefaultContactService implements ContactService
{
    use OnlyCreate;
    use RunsLifecycle;

    /**
     * Create a new contact service instance.
     *
     * @param ContactService $delegate The underlying contact service
     * @param Lifecycle $lifecycle The lifecycle manager
     */
    public function __construct(
        protected CoreContactService $delegate,
        protected Lifecycle $lifecycle,
    ) {}

    /**
     * Create a new contact identity
     *
     * @param ContactCreateData $data The data for creating a contact
     * @return string|int Identity ID
     */
    public function create(ContactCreateData $data): string|int
    {
        return $this->runLifecycle(
            context: $data,
            callback: fn() => $this->handleCreate($data),
        );
    }

    /**
     * @param ContactCreateData $data
     *
     * @return mixed
     */
    protected function handleCreate(ContactCreateData $data): mixed
    {
        return $this->delegate->create($data);
    }

    public function delete(string $id): bool
    {
        return false;
    }

    public function restore(string $id): bool
    {
        return false;
    }

    /**
     * Get the event key for contact lifecycle events.
     *
     * @return string
     */
    protected function eventKey(): string
    {
        return 'contact';
    }
}
