<?php

namespace App\Services;

use App\Contracts\Repositories\InvitationRepository;
use App\Contracts\Services\InvitationService as InvitationServiceContract;
use App\Data\InvitationData;
use App\Data\InvitationAcceptanceData;
use App\Data\InvitationConfigData;
use App\Factories\InvitationStrategyFactory;
use App\Models\User;
use App\Services\Base\BaseService;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;
use Kwidoo\Lifecycle\Data\LifecycleData;
use Kwidoo\Mere\Contracts\MenuService;

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
        $lifecycleData = new LifecycleData(
            action: 'sendInvite',
            resource: $this->eventKey(),
            context: $data
        );

        $this->lifecycle->run(
            $lifecycleData,
            function () use ($data) {
                $this->handleSend($data);
            },
            $this->options
        );
    }

    public function accept(InvitationAcceptanceData $data): User
    {
        $lifecycleData = new LifecycleData(
            action: 'acceptInvite',
            resource: $this->eventKey(),
            context: $data
        );

        return $this->lifecycle->run(
            $lifecycleData,
            function () use ($data) {
                return $this->handleAccept($data);
            },
            $this->options->withoutAuth()
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
