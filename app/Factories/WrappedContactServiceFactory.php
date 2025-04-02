<?php

namespace App\Factories;

use App\Services\Wrapped\WrappedContactService;
use Kwidoo\Contacts\Contracts\Contactable;
use Kwidoo\Contacts\Contracts\ContactServiceFactory;
use Kwidoo\Mere\Contracts\Lifecycle;

class WrappedContactServiceFactory
{
    public function __construct(
        protected ContactServiceFactory $csf,
        protected Lifecycle $defaultLifecycle
    ) {}

    /**
     * @param Contactable $model
     * @param Lifecycle $lifecycle
     *
     * @return WrappedContactService
     */
    public function make(Contactable $model, Lifecycle $lifecycle): WrappedContactService
    {
        $delegate = $this->csf->make($model);

        return new WrappedContactService($delegate, $lifecycle ?? $this->defaultLifecycle);
    }
}
