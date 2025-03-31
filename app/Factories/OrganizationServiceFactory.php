<?php

namespace App\Factories;

use App\Contracts\Services\OrganizationService;
use Kwidoo\Mere\Contracts\Lifecycle;
use App\Models\User;

class OrganizationServiceFactory
{
    protected string $slug = 'main';
    public function __construct(
        protected Lifecycle $defaultLifecycle,
    ) {}

    /**
     * @param User $user
     * @param Lifecycle|null $lifecycle
     *
     * @return OrganizationService
     */
    public function make(User $user, ?Lifecycle $lifecycle = null): OrganizationService
    {
        return app()->make(OrganizationService::class, [
            'user' => $user,
            'lifecycle' => $lifecycle ?? $this->defaultLifecycle,
            'slug' => $this->slug,
        ]);
    }

    /**
     * @param string $slug
     *
     * @return self
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
