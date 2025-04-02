<?php

namespace App\Strategies\Organization;

use App\Contracts\Repositories\OrganizationRepository;
use App\Contracts\Services\Strategy;
use App\Data\RegistrationData;
use App\Enums\RegistrationFlow;
use App\Services\OrganizationService;
use Illuminate\Validation\ValidationException;

class InitialBootstrapStrategy implements Strategy
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

        $data->organization = $this->organizationService->createInitialOrganization($data);
    }
}
