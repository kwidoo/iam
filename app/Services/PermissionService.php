<?php

namespace App\Services;

use App\Contracts\Services\PermissionService as PermissionServiceContract;
use App\Contracts\Repositories\PermissionRepository;
use App\Contracts\Repositories\UserRepository;
use App\Models\User;
use Exception;
use Kwidoo\Mere\Contracts\Lifecycle;
use Kwidoo\Mere\Services\BaseService;
use Kwidoo\Mere\Contracts\MenuService;
use Spatie\Permission\Contracts\Permission;

class PermissionService extends BaseService implements PermissionServiceContract
{
    public function __construct(
        MenuService $menuService,
        PermissionRepository $repository,
        Lifecycle $lifecycle,
        protected UserRepository $userRepository
    ) {
        parent::__construct($menuService, $repository, $lifecycle);
    }

    protected function eventKey(): string
    {
        return 'permission';
    }

    public function givePermission(Permission $permission, string $userId): void
    {
        $this->lifecycle->run(
            action: 'create',
            resource: $this->eventKey(),
            context: ['permissionId' => $permission->id, 'userId' => $userId],
            callback: fn() => $this->handleGivePermission($permission, $userId)
        );
    }

    protected function handleGivePermission(Permission $permission, string $userId): User
    {
        $user = $this->userRepository->find($userId);
        if ($user) {
            $user->givePermissionTo($permission);
            return $user;
        }

        throw new Exception('User not found');
    }
}
