<?php

namespace App\Factories;

use App\Contracts\Repositories\OrganizationRepository;

class ProfileOrganizationFactory
{
    // @todo: update logic
    public function make(string $name)
    {
        return app()->make(OrganizationRepository::class)->findByField('name', $name)->first();
    }
}
