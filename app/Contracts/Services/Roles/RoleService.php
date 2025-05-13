<?php

namespace App\Contracts\Services\Roles;

use Kwidoo\Mere\Contracts\BaseService;
use Spatie\Permission\Contracts\Role;

interface RoleService extends BaseService
{
    public function getByName(string $name, ?string $organizationId = null): Role|null;
}
