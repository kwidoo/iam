<?php

namespace App\Contracts;

interface AclService
{
    public function createOwnerRule($entity);
}
