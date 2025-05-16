<?php

namespace App\Services\Organizations;

use App\Contracts\Services\OrganizationCreateService;
use App\Contracts\Services\Organizations\OrganizationService;
use App\Contracts\Services\Organizations\RoleSetupService;
use App\Data\Organizations\OrganizationCreateData;
use App\Enums\OrganizationFlow;
use Kwidoo\Mere\Contracts\Models\OrganizationInterface;
use Kwidoo\Mere\Contracts\Repositories\OrganizationRepository;
use Spatie\LaravelData\Contracts\BaseData;
use Illuminate\Support\Str;

class UserCreatesOrg implements OrganizationCreateService
{
    /**
     * Initialize the strategy with required service.
     *
     * @param OrganizationService $service Organization service instance
     */
    public function __construct(
        protected OrganizationService $service,
        protected RoleSetupService $roleSetupService,
        protected OrganizationRepository $repository,
    ) {}

    /**
     * Get the registration flow type this strategy handles.
     *
     * @return OrganizationFlow
     */
    public function key(): OrganizationFlow
    {
        return OrganizationFlow::USER_CREATES_ORG;
    }

    /**
     * Create a new organization for the user during registration.
     * Sets up the organization with default settings and associates the user.
     *
     * @param \App\Data\Registration\DefaultRegistrationData $data Registration data containing user and org info
     *
     * @return \App\Models\Organization
     */
    public function create(BaseData $data): OrganizationInterface
    {
        $organization = $this->service->create(OrganizationCreateData::from([
            'name' => $data->name ?? $data->orgName ?? "{$data->fname} {$data->lname}'s Organization",
            'slug' => $this->generateSlug(),
            'ownerId' => $data->user->id,
            'flow' => $this->key(),
        ]));

        $this->roleSetupService->initialize(
            $organization,
            $data,
        );

        return $organization;
    }

    /**
     * @return string
     */
    protected function generateSlug(): string
    {
        do {
            $slug = config('iam.orgPrefix', 'org:') . Str::substr(md5(Str::lower(Str::random(20))), 5, 8);
        } while ($this->repository->findByField('slug', $slug)->isNotEmpty());
        return $slug;
    }
}
