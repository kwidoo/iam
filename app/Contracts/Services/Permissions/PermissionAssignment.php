<?php

namespace App\Services\Permissions;

use App\Data\GivePermissionData;

interface PermissionAssignment
{
    public function assign(GivePermissionData $data): void;
    public function revoke(GivePermissionData $data): void;
}
