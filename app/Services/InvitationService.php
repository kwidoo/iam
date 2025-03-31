<?php

namespace App\Services;

use App\Contracts\Repositories\InvitationRepository;
use App\Contracts\Services\InvitationService as InvitationServiceContract;
use Kwidoo\Mere\Contracts\Lifecycle;
use Kwidoo\Mere\Contracts\MenuService;
use Kwidoo\Mere\Services\BaseService;

class InvitationService extends BaseService implements InvitationServiceContract
{
    public function __construct(
        MenuService $menuService,
        InvitationRepository $repository,
        Lifecycle $lifecycle,

    ) {

        parent::__construct($menuService, $repository, $lifecycle);
    }
    protected function eventKey(): string
    {
        return 'invitation';
    }
}
