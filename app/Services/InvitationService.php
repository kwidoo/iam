<?php

namespace App\Services;

use App\Contracts\Repositories\InvitationRepository;
use App\Contracts\Services\InvitationService as InvitationServiceContract;
use Kwidoo\Mere\Contracts\MenuService;
use Kwidoo\Mere\Services\BaseService;

class InvitationService extends BaseService implements InvitationServiceContract
{
    public function __construct(
        MenuService $menuService,
        InvitationRepository $repository
    ) {

        parent::__construct($menuService, $repository);
    }
    protected function eventKey(): string
    {
        return 'invitation';
    }
}
