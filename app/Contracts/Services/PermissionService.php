<?php

namespace App\Contracts\Services;

use Kwidoo\Mere\Contracts\BaseService;
use Spatie\Permission\Contracts\Permission;

interface PermissionService extends BaseService
{
    public function givePermission(Permission $permission, string $userId): void;
}
