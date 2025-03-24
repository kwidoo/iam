<?php

namespace App\Services;

use App\Contracts\Services\MicroserviceService as MicroserviceServiceContract;
use App\Contracts\Repositories\MicroserviceRepository;
use Kwidoo\Mere\Services\BaseService;
use Kwidoo\Mere\Contracts\MenuService;

class MicroserviceService extends BaseService implements MicroserviceServiceContract
{
    public function __construct(MenuService $menuService, MicroserviceRepository $repository)
    {
        parent::__construct($menuService, $repository);
    }

    protected function eventKey(): string
    {
        return 'microservice';
    }
}
