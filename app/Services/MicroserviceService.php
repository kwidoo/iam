<?php

namespace App\Services;

use App\Contracts\Services\MicroserviceService as MicroserviceServiceContract;
use App\Contracts\Repositories\MicroserviceRepository;
use App\Services\Base\BaseService;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;
use Kwidoo\Mere\Contracts\MenuService;

class MicroserviceService extends BaseService implements MicroserviceServiceContract
{
    public function __construct(
        MenuService $menuService,
        MicroserviceRepository $repository,
        Lifecycle $lifecycle,
    ) {
        parent::__construct($menuService, $repository, $lifecycle);
    }

    protected function eventKey(): string
    {
        return 'microservice';
    }
}
