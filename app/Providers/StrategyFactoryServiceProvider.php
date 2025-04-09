<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Factories\{
    AccessAssignmentFactory,
    AuthorizerFactory,
    RoleAssignmentStrategyFactory,
    PermissionAssignmentStrategyFactory,
};
use App\Strategies\Access\{
    AssignAdminRoleStrategy,
    AssignDefaultUserRoleStrategy,
    GrantAdminPermissionStrategy,
    GrantDefaultUserPermissionStrategy,
};
use Kwidoo\Mere\Contracts\{
    AuthorizerFactory as AuthorizerFactoryContract,
    AccessAssignmentFactory as AccessAssignmentFactoryContract,
};

class StrategyFactoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RoleAssignmentStrategyFactory::class, function ($app) {
            return new RoleAssignmentStrategyFactory([
                'assign.admin.role' => AssignAdminRoleStrategy::class,
                'assign.default.role' => AssignDefaultUserRoleStrategy::class,
            ]);
        });

        $this->app->singleton(PermissionAssignmentStrategyFactory::class, function ($app) {
            return new PermissionAssignmentStrategyFactory([
                'grant.admin.permissions' => GrantAdminPermissionStrategy::class,
                'grant.default.permissions' => GrantDefaultUserPermissionStrategy::class,
            ]);
        });

        $this->app->bind(AuthorizerFactoryContract::class, AuthorizerFactory::class);
        $this->app->bind(AccessAssignmentFactoryContract::class, AccessAssignmentFactory::class);
    }
}
