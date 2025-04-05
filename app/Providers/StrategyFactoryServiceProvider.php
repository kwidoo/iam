<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Factories\{
    RoleAssignmentStrategyFactory,
    PermissionAssignmentStrategyFactory,
};
use App\Access\Roles\{
    AssignAdminRoleStrategy,
    AssignDefaultUserRoleStrategy,
};
use App\Access\Permissions\{
    GrantAdminPermissionStrategy,
    GrantDefaultUserPermissionStrategy,
};

class StrategyFactoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RoleAssignmentStrategyFactory::class, function ($app) {
            return new RoleAssignmentStrategyFactory([
                'assign.admin.role' => $app->make(AssignAdminRoleStrategy::class),
                'assign.default.role' => $app->make(AssignDefaultUserRoleStrategy::class),
            ]);
        });

        $this->app->singleton(PermissionAssignmentStrategyFactory::class, function ($app) {
            return new PermissionAssignmentStrategyFactory([
                'grant.admin.permissions' => $app->make(GrantAdminPermissionStrategy::class),
                'grant.default.permissions' => $app->make(GrantDefaultUserPermissionStrategy::class),
            ]);
        });
    }
}
