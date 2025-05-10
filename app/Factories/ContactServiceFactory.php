<?php

namespace App\Factories;

use App\Services\ContactService;
use Kwidoo\Contacts\Contracts\Contactable;
use Kwidoo\Contacts\Contracts\ContactServiceFactory as BaseContractServiceFactory;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;

class ContactServiceFactory
{
    public function __construct(
        protected BaseContractServiceFactory $csf,
        protected Lifecycle $defaultLifecycle
    ) {}

    /**
     * @param Contactable $model
     * @param Lifecycle $lifecycle
     *
     * @return ContactService
     */
    public function make(Contactable $model, Lifecycle $lifecycle): ContactService
    {
        $delegate = $this->csf->make($model);

        return new ContactService($delegate, $lifecycle ?? $this->defaultLifecycle);
    }
}
