<?php

namespace App\Contracts\Services;

use Kwidoo\Mere\Contracts\BaseService;
use Spatie\Permission\Contracts\Role;

interface RoleService extends BaseService
{
    public function assignRole(Role $role, string $userId): mixed;
    public function getByName(string $name): Role;
}
