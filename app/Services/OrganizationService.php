<?php

namespace App\Services;

use App\Contracts\Repositories\OrganizationRepository;
use App\Contracts\Services\OrganizationService as OrganizationServiceContract;
use App\Data\RegistrationData;
use App\Factories\PermissionServiceFactory;
use App\Factories\RoleServiceFactory;
use App\Models\Organization;
use Kwidoo\Mere\Contracts\Lifecycle;
use Kwidoo\Mere\Contracts\MenuService;
use Kwidoo\Mere\Services\BaseService;
use Illuminate\Support\Str;

class OrganizationService extends BaseService implements OrganizationServiceContract
{
    protected ?Organization $organization;

    public function __construct(
        MenuService $menuService,
        OrganizationRepository $repository,
        Lifecycle $lifecycle,
        string $slug = 'main',
        protected RoleServiceFactory $rsf,
        protected PermissionServiceFactory $psf,
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
        $roleService = $this->rsf->make($this->lifecycle);
        $permissionService = $this->psf->make($this->lifecycle);

        do {
            $organizationSlug = config('iam.orgPrefix', 'org-') . md5(Str::lower(Str::random(20)));
        } while ($this->repository->findByField('slug', $organizationSlug)->isNotEmpty());

        $organization = $this->create([
            'name' => "{$data->fname} {$data->lname} organization",
            'slug' => $organizationSlug,
            'owner_id' => $data->user->id,
            'role' => 'owner',
        ]);

        $role = $roleService->create([
            'name' => $organizationSlug . '-admin',
            'organization_id' => $organization->id,
            'description' => "{$data->fname} {$data->lname}\'s organization role with full permissions.",
            'guard_name' => 'web',
        ]);

        $role = $roleService->create([
            'name' => $organizationSlug . '-user',
            'organization_id' => $organization->id,
            'description' => "{$data->fname} {$data->lname}\'s organization role with full permissions.",
            'guard_name' => 'web',
        ]);

        $permission = $permissionService->create([
            'name' => $organizationSlug . '-admin',
            'organization_id' => $organization->id,
            'description' => 'Allows the user to manage the {$data->fname} {$data->lname}\'s organization.',
            'guard_name' => 'web',
        ]);

        $roleService->assignRole($role, $data->user->id);
        $permissionService->givePermission($permission, $data->user->id);

        return $organization;
    }

    /**
     * @param RegistrationData $data
     *
     * @return Organization
     */
    public function loadDefault(RegistrationData $data): Organization
    {
        $this->lifecycle = $this->lifecycle->withoutAuth();
        $this->organization = $this->repository->findByField('slug', 'main')->first();

        if ($this->organization) {
            $roleService = $this->rsf->make($this->lifecycle);
            $role = $roleService->getByName('user');
            $roleService->assignRole($role, $data->user->id);

            return $this->organization;
        }

        return $this->createDefaultForUser($data);
    }
}
