<?php

namespace App\Factories;

use App\Contracts\Services\PermissionService;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;

class PermissionServiceFactory
{
    public function __construct(
        protected Lifecycle $defaultLifecycle,
    ) {}

    /**
     * Create an instance of PermissionService.
     *
     * @param Lifecycle|null $lifecycle
     * @return PermissionService
     */
    public function make(?Lifecycle $lifecycle = null): PermissionService
    {
        // You can add any dependencies or configurations here
        return app()->make(PermissionService::class, ['lifecycle' => $lifecycle ?? $this->defaultLifecycle,]);
    }
}
