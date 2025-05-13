<?php

namespace App\Services\Organizations;

use App\Contracts\Services\OrganizationService;
use App\Contracts\Services\UserService;
use Kwidoo\Mere\Data\ShowQueryData;

class UserAndOrganizationResolver
{
    public function __construct(
        protected UserService $userService,
        protected OrganizationService $organizationService,
    ) {
        // Constructor logic if needed
    }
    /**
     * @param string $userId
     * @param string $organizationId
     * @return array<int, \App\Models\User|\App\Models\Organization>
     */
    public function resolve(string $userId, string $organizationId): array
    {
        $user = $this->userService->getById(ShowQueryData::from(id: $userId));
        $organization = $this->organizationService->getById(ShowQueryData::from(id: $organizationId));

        return [$user, $organization];
    }
}
