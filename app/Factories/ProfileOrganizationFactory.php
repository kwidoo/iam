<?php

namespace App\Factories;

use App\Contracts\Repositories\OrganizationRepository;
use App\Models\Organization;

class ProfileOrganizationFactory
{
    public function __construct(
        protected OrganizationRepository $organizationRepository,
    ) {}

    /**
     * @param string $name
     *
     * @return Organization
     */
    public function make(string $name): Organization
    {
        return $this->organizationRepository->findByField('name', $name)->first();
    }
}
