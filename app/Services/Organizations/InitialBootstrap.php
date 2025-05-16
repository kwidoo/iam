<?php

namespace App\Services\Organizations;

use App\Data\Organizations\OrganizationCreateData;
use App\Enums\OrganizationFlow;
use Kwidoo\Mere\Contracts\Models\OrganizationInterface;
use App\Contracts\Services\Organizations\OrganizationService;
use App\Contracts\Services\Organizations\RoleSetupService;
use Spatie\LaravelData\Contracts\BaseData;

class InitialBootstrap extends UserCreatesOrg
{
    public function __construct(
        protected OrganizationService $service,
        protected RoleSetupService $roleSetupService,
    ) {
        //
    }

    public function key(): OrganizationFlow
    {
        return OrganizationFlow::INITIAL_BOOTSTRAP;
    }

    /**
     * @param \App\Data\Registration\DefaultRegistrationData $data Registration data containing user and org info
     *
     * @return \App\Models\Organization
     */
    public function create(BaseData $data): OrganizationInterface
    {
        $initial = OrganizationCreateData::from([
            'name' => "Main organization",
            'slug' => 'main',
            'ownerId' => $data->user->id,
            'flow' => $this->key(),
        ]);

        $organization = $this->service->create($initial);

        $this->roleSetupService->initialize(
            $organization,
            $initial,
        );

        return $organization;
    }
}
