<?php

namespace App\Services;

use App\Contracts\Repositories\InvitationRepository;
use App\Contracts\Services\InvitationService as InvitationServiceContract;
use Kwidoo\Mere\Contracts\Lifecycle;
use Kwidoo\Mere\Contracts\MenuService;
use Kwidoo\Mere\Services\BaseService;
use App\Models\User;
use App\Data\InvitationData;
use App\Data\InvitationAcceptanceData;
use App\Data\InvitationConfigData;
use App\Factories\InvitationStrategyFactory;

class InvitationService extends BaseService implements InvitationServiceContract
{
    public function __construct(
        MenuService $menuService,
        InvitationRepository $repository,
        Lifecycle $lifecycle,
        protected InvitationStrategyFactory $factory,
    ) {

        parent::__construct($menuService, $repository, $lifecycle);
    }
    protected function eventKey(): string
    {
        return 'invitation';
    }

    public function send(InvitationData $data): void
    {
        $this->lifecycle
            ->run(
                action: 'sendInvite',
                resource: $this->eventKey(),
                context: $data,
                callback: fn() => $this->handleSend($data),
            );
    }

    public function accept(InvitationAcceptanceData $data): User
    {
        return $this->lifecycle
            ->withoutAuth()
            ->run(
                action: 'acceptInvite',
                resource: $this->eventKey(),
                context: $data,
                callback: fn() => $this->handleAccept($data),
            );
    }

    protected function handleSend(InvitationData $data): void
    {
        $config = InvitationConfigData::from($data);
        $strategy = $this->factory->make($config);
        $strategy->send($data);
    }

    protected function handleAccept(InvitationAcceptanceData $data): User
    {
        $strategy = $this->factory->makeFromToken($data->token);
        return $strategy->accept($data);
    }
}
