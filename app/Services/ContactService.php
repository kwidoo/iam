<?php

namespace App\Services;

use App\Services\Traits\OnlyCreate;
use Kwidoo\Contacts\Contracts\ContactService as ContactServiceContract;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;
use Kwidoo\Lifecycle\Data\LifecycleData;
use Kwidoo\Lifecycle\Data\LifecycleOptionsData;

class ContactService implements ContactServiceContract
{
    use OnlyCreate;

    /**
     * Lifecycle options for managing cross-cutting concerns
     *
     * @var LifecycleOptionsData
     */
    protected LifecycleOptionsData $options;

    /**
     * Create a new contact service instance.
     *
     * @param ContactServiceContract $delegate The underlying contact service
     * @param Lifecycle $lifecycle The lifecycle manager
     */
    public function __construct(
        protected ContactServiceContract $delegate,
        protected Lifecycle $lifecycle,
    ) {
        $this->options = new LifecycleOptionsData();
    }

    /**
     * Create a new contact identity
     *
     * @param string $type Type of identity (email, phone, etc.)
     * @param string $value Value of the identity
     * @return string|int Identity ID
     */
    public function create(string $type, string $value): string|int
    {
        // Create lifecycle data for the operation
        $lifecycleData = new LifecycleData(
            action: 'createIdentity',
            resource: 'identity',
            context: [
                'type' => $type,
                'value' => $value
            ]
        );

        // Execute within lifecycle with default options
        return $this->lifecycle->run(
            $lifecycleData,
            function () use ($type, $value) {
                return $this->delegate->create($type, $value);
            },
            $this->options
        );
    }
}
