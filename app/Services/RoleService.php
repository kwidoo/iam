<?php

namespace App\Services;

use App\Contracts\Services\RoleService as RoleServiceContract;
use App\Contracts\Repositories\RoleRepository;
use App\Contracts\Repositories\UserRepository;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Kwidoo\Mere\Contracts\Lifecycle;
use Kwidoo\Mere\Services\BaseService;
use Kwidoo\Mere\Contracts\MenuService;
use Spatie\Permission\Contracts\Role;

class RoleService extends BaseService implements RoleServiceContract
{
    public function __construct(
        MenuService $menuService,
        RoleRepository $repository,
        Lifecycle $lifecycle,
        protected UserRepository $userRepository,
    ) {
        parent::__construct($menuService, $repository, $lifecycle);
    }

    protected function eventKey(): string
    {
        return 'role';
    }

    public function getByName(string $name, ?string $organizationId = null): ?Role
    {
        $query = $this->repository->findByField('name', $name);

        if ($organizationId) {
            $query = $query->where('organization_id', $organizationId);
        }

        return $query->first();
    }

    /**
     * @param Role $role
     * @param string $userId
     *
     * @return mixed
     */
    public function assignRole(Role $role, string $userId, string $organizationId): mixed
    {
        return $this->lifecycle->run(
            action: 'create',
            resource: $this->eventKey(),
            context: $this->lifecycle->context(),
            callback: fn() => $this->handleAssignRole($role, $userId, $organizationId)
        );
    }

    /**
     * @param Role $role
     * @param string $userId
     *
     * @return User
     */
    protected function handleAssignRole(Role $role, string $userId, string $organizationId): User
    {
        /** @var User $user */
        $user = $this->userRepository->find($userId);

        if (!$user) {
            throw new Exception("User with ID [$userId] not found.");
        }

        // Directly insert into the pivot with org ID
        DB::table('model_has_roles')->updateOrInsert([
            'role_id' => $role->id,
            config('permission.column_names.model_morph_key') => $user->getKey(),
            'model_type' => $user->getMorphClass(),
            // 'organization_id' => $organizationId,
        ]);

        return $user;
    }
}
