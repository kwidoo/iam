<?php

namespace App\Services\Permissions;

use App\Data\Organizations\UserOrganizationData;

interface PermissionAssignment
{
    public function assign(UserOrganizationData $data): void;
    public function revoke(UserOrganizationData $data): void;
}
