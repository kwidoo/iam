<?php

namespace App\Http\Controllers;

use Kwidoo\Mere\Data\ListQueryData;
use Kwidoo\Mere\Http\Controllers\ResourceController;
use Kwidoo\Mere\Http\Resources\ResourceCollection;

class RoleController extends ResourceController
{
    public function index(ListQueryData $data): ResourceCollection
    {
        $data->resource = 'role';
        return parent::index($data);
    }
}
