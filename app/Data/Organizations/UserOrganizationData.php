<?php

namespace App\Data\Organizations;

use Kwidoo\Mere\Contracts\Models\OrganizationInterface;
use Kwidoo\Mere\Contracts\Models\UserInterface;
use Spatie\LaravelData\Data;

/**
 * @property \App\Models\Organization $organization
 */
class UserOrganizationData extends Data
{
    public function __construct(
        public UserInterface $user,
        public OrganizationInterface $organization,
    ) {}
}
