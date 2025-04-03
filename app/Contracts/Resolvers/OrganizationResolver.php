<?php

namespace App\Contracts\Resolvers;

use App\Enums\RegistrationFlow;
use App\Models\Organization;

interface OrganizationResolver
{
    public function resolve(?string $input = null): ?Organization;
}
