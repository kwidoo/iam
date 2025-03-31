<?php

namespace App\Factories;

use App\Contracts\Services\RoleService;
use Kwidoo\Mere\Contracts\Lifecycle;

class RoleServiceFactory
{
    public function __construct(
        protected Lifecycle $defaultLifecycle,
    ) {}
    /**
     * Create an instance of RoleService.
     *
     * @return RoleService
     */
    public function make(?Lifecycle $lifecycle = null): RoleService
    {
        // You can add any dependencies or configurations here
        return app()->make(RoleService::class, ['lifecycle' => $lifecycle ?? $this->defaultLifecycle,]);
    }
}
