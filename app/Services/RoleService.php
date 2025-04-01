<?php

namespace App\Services;

use App\Contracts\Services\RoleService as RoleServiceContract;
use App\Contracts\Repositories\RoleRepository;
use App\Contracts\Repositories\UserRepository;
use App\Models\User;
use Exception;
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

    public function getByName(string $name): Role
    {
        return $this->repository->findByField('name', $name)->first();
    }

    /**
     * @param Role $role
     * @param string $userId
     *
     * @return mixed
     */
    public function assignRole(Role $role, string $userId): mixed
    {
        return $this->lifecycle->run(
            action: 'create',
            resource: $this->eventKey(),
            context: ['roleId' => $role->id, 'userId' => $userId],
            callback: fn() => $this->handleAssignRole($role, $userId)
        );
    }

    /**
     * @param Role $role
     * @param string $userId
     *
     * @return User
     */
    protected function handleAssignRole(Role $role, string $userId): User
    {
        $user = $this->userRepository->find($userId);
        if ($user) {
            $user->assignRole($role);
            return $user;
        }
        throw new Exception('User not found');
    }
}
