<?php

namespace App\Factories;

use App\Contracts\Services\ProfileService;
use App\Models\User;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;

class ProfileServiceFactory
{
    public function __construct(
        protected Lifecycle $defaultLifecycle,
    ) {}

    /**
     * @param User $user
     * @param Lifecycle|null $lifecycle
     *
     * @return ProfileService
     */
    public function make(User $user, ?Lifecycle $lifecycle = null): ProfileService
    {
        return app()->make(ProfileService::class, [
            'user' => $user,
            'lifecycle' => $lifecycle ?? $this->defaultLifecycle,
        ]);
    }
}
