<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;

interface AclService
{
    /**
     * @param Model $entity
     *
     * @return void
     */
    public function createOwnerRule($entity): void;
}
