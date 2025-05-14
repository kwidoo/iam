<?php

namespace App\Strategies\Organization;

use Kwidoo\Mere\Contracts\Repositories\OrganizationRepository;
use App\Contracts\Services\OrganizationCreateService;
use App\Data\Organizations\OrganizationCreateData;
use Kwidoo\Mere\Contracts\Data\RegistrationData;
use App\Enums\RegistrationFlow;
use App\Services\OrganizationService;
use Illuminate\Validation\ValidationException;

class InitialBootstrap implements OrganizationCreateService
{
    public function __construct(
        protected OrganizationService $organizationService,
        protected OrganizationRepository $repository,

    ) {}

    public function key(): RegistrationFlow
    {
        return RegistrationFlow::INITIAL_BOOTSTRAP;
    }

    public function create(RegistrationData $data): void
    {
        if ($this->repository->count() > 0) {
            throw ValidationException::withMessages([
                'strategy' => 'Initial bootstrap is not allowed — organizations already exist.',
            ]);
        }

        $data->organization = $this->organizationService->create(OrganizationCreateData::from(
            name: "Main organization",
            slug: 'main',
            ownerId: $data->user->id,
        ));
    }
}
