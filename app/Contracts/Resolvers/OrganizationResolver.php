<?php

namespace App\Contracts\Resolvers;

use Kwidoo\Mere\Contracts\Models\OrganizationInterface;

interface OrganizationResolver
{
    public function resolve(?string $input = null): ?OrganizationInterface;
}
