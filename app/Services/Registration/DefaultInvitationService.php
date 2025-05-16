<?php

namespace App\Services;

use App\Contracts\Services\InvitationService;
use App\Criteria\ByContact;
use App\Criteria\Invitations\ByInviterId;
use App\Criteria\ByOrganizationId;
use App\Criteria\Invitations\PendingInvitations;
use App\Data\InvitationData;
use App\Data\InvitationAcceptanceData;
use App\Data\InvitationConfigData;
use App\Enums\InviteMethod;
use App\Factories\InvitationStrategyFactory;
use App\Services\BaseService;
use App\Resolvers\UserResolver;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;
use Kwidoo\Mere\Contracts\Models\InvitationInterface;
use Kwidoo\Mere\Contracts\Services\MenuService;
use Kwidoo\Mere\Contracts\Repositories\InvitationRepository;
use Kwidoo\Lifecycle\Traits\RunsLifecycle;

class DefaultInvitationService extends BaseService implements InvitationService
{
    use RunsLifecycle;
    protected $invitationExpiryDays = 7;

    public function __construct(
        MenuService $menuService,
        InvitationRepository $repository,
        Lifecycle $lifecycle,
        protected InvitationStrategyFactory $factory,
        protected UserResolver $resolver,
    ) {
        parent::__construct($menuService, $repository, $lifecycle);
    }

    protected function eventKey(): string
    {
        return 'invitation';
    }

    /**
     * @param InvitationData $data
     *
     * @return void
     */
    public function send(InvitationData $data): void
    {
        $this->runLifecycle(
            context: $data,
            callback: fn() => $this->handleSend($data),
        );
    }

    /**
     * @param InvitationAcceptanceData $data
     *
     * @return \App\Models\Invitation
     */
    public function accept(InvitationAcceptanceData $data): InvitationInterface
    {
        $this->withLifecycleOptions($this->options->withoutAuth());

        return $this->runLifecycle(
            context: $data,
            callback: fn() => $this->handleAccept($data),
        );
    }

    /**
     * @param InvitationData $data
     *
     * @return \App\Models\Invitation
     */
    protected function handleSend(InvitationData $data): InvitationInterface
    {
        // Use multiple criteria to check for existing invitations
        $this->repository->pushCriteria(new ByContact(
            $data->method,
            $data->value
        ));
        $this->repository->pushCriteria(new ByOrganizationId($data->organizationId));
        $this->repository->pushCriteria(new ByInviterId($data->inviterId));
        $this->repository->pushCriteria(new PendingInvitations());
        $existingInvitation = $this->repository->first();

        if ($existingInvitation) {
            return $existingInvitation;
        }

        $user = $this->resolver->resolve($data->method, $data->value);

        $config = InvitationConfigData::fromInvitation($data);

        if ($user) {
            $config = InvitationConfigData::from([
                'method' => InviteMethod::EXISTING_USER,
            ]);
        }

        $strategy = $this->factory->make($config);

        return $strategy->send($data);
    }

    /**
     * @param InvitationAcceptanceData $data
     *
     * @return UserInterface|null
     */
    protected function handleAccept(InvitationAcceptanceData $data): ?UserInterface
    {
        $strategy = $this->factory->makeFromToken($data->token);

        return $strategy->accept($data);
    }
}
