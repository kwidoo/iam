<?php

namespace App\Services;

use App\Contracts\Repositories\OrganizationRepository;
use App\Contracts\Services\OrganizationService as OrganizationServiceContract;
use App\Data\RegistrationData;
use App\Models\Organization;
use Kwidoo\Mere\Contracts\Lifecycle;
use Kwidoo\Mere\Contracts\MenuService;
use Kwidoo\Mere\Services\BaseService;

class OrganizationService extends BaseService implements OrganizationServiceContract
{
    protected ?Organization $organization;

    public function __construct(
        MenuService $menuService,
        OrganizationRepository $repository,
        Lifecycle $lifecycle,
        string $slug = 'main',
        protected RoleService $roleService,
        protected PermissionService $permissionService,


    ) {
        parent::__construct($menuService, $repository, $lifecycle);
        $this->organization = $repository->findByField('slug', $slug)->first();
    }

    protected function eventKey(): string
    {
        return 'organization';
    }

    public function createDefaultForUser(RegistrationData $data): Organization
    {
        $this->lifecycle = $this->lifecycle->withoutAuth();

        $organization = $this->create([
            'name' => "{$data->fname} {$data->lname}'s Organization",
            'owner_id' => $data->userId,
            'type' => 'self-created',
        ]);

        // Optionally assign role/permissions to user here:
        // $this->roleService->assignRole($user, 'owner', $organization);
        //$this->permissionService->assignDefaultPermissions($user, $organization);

        return $organization;
    }
}
