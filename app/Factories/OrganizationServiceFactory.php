<?php

namespace App\Factories;

use App\Contracts\Services\OrganizationService;
use App\Models\User;
use Kwidoo\Mere\Contracts\Lifecycle;

class OrganizationServiceFactory
{
    public function __construct(
        protected Lifecycle $defaultLifecycle,
    ) {}

    public function make(User $user, ?Lifecycle $lifecycle = null): OrganizationService
    {
        return app()->make(OrganizationService::class, [
            'user' => $user,
            'lifecycle' => $lifecycle ?? $this->defaultLifecycle,
            'slug' => 'main',
        ]);
    }
}
