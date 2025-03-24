<?php

namespace App\Guards;

use App\Exceptions\UserCreationException;
use App\Models\Organization;

class OrganizationGuard
{
    public function checkCanRegister(?Organization $organization, array $data): void
    {
        return;
        if ($organization && $organization->users()->where('user_id', $data['user_id'])->exists()) {
            return;
        }

        throw new UserCreationException('User is not a member of the organization.');
    }
}
