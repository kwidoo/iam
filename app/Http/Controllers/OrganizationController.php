<?php

namespace App\Http\Controllers;

use Kwidoo\Mere\Data\ListQueryData;
use Kwidoo\Mere\Http\Controllers\ResourceController;
use Kwidoo\Mere\Http\Resources\ResourceCollection;

class OrganizationController extends ResourceController
{
    public function index(ListQueryData $data): ResourceCollection
    {
        $data->resource = 'organization';
        return parent::index($data);
    }
}
