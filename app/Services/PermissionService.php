<?php

namespace App\Services;

use App\Contracts\Services\PermissionService as PermissionServiceContract;
use App\Contracts\Repositories\PermissionRepository;
use App\Contracts\Repositories\UserRepository;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
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

    public function getByName(string $name, ?string $organizationId = null): Permission
    {
        $query = $this->repository->findByField('name', $name);

        if ($organizationId) {
            $query = $query->where('organization_id', $organizationId);
        }

        return $query->first();
    }

    public function givePermission(Permission $permission, string $userId, string $organizationId): mixed
    {
        return $this->lifecycle->run(
            action: 'assign',
            resource: $this->eventKey(),
            context: $this->lifecycle->context(),
            callback: fn() => $this->handleGivePermission($permission, $userId, $organizationId)
        );
    }

    protected function handleGivePermission(Permission $permission, string $userId, string $organizationId): User
    {
        /** @var User $user */
        $user = $this->userRepository->find($userId);

        if (!$user) {
            throw new Exception("User with ID [$userId] not found.");
        }

        DB::table('model_has_permissions')->updateOrInsert([
            'permission_id' => $permission->id,
            config('permission.column_names.model_morph_key') => $user->getKey(),
            'model_type' => $user->getMorphClass(),
            //'organization_id' => $organizationId,
        ]);

        return $user;
    }
}
