<?php

namespace App\Resolvers;

use App\Contracts\Access\RoleAssignmentStrategy;
use App\Contracts\Access\PermissionAssignmentStrategy;
use App\Data\AccessAssignmentData;
use App\Factories\RoleAssignmentStrategyFactory;
use App\Factories\PermissionAssignmentStrategyFactory;
use App\Enums\RegistrationFlow;

class AccessAssignmentStrategyResolver
{
    public function __construct(
        protected RoleAssignmentStrategyFactory $roleFactory,
        protected PermissionAssignmentStrategyFactory $permissionFactory,
    ) {}

    /**
     * @return array{RoleAssignmentStrategy, PermissionAssignmentStrategy}
     */
    public function resolve(AccessAssignmentData $context): array
    {
        if (in_array($context->actor, ['admin', 'super_admin'])) {
            if ($context->flow === RegistrationFlow::USER_CREATES_ORG) {
                return [
                    $this->roleFactory->make('assign.admin.role'),
                    $this->permissionFactory->make('grant.admin.permissions'),
                ];
            }

            if ($context->flow === RegistrationFlow::USER_JOINS_USER_ORG) {
                return [
                    $this->roleFactory->make('assign.default.role'),
                    $this->permissionFactory->make('grant.default.permissions'),
                ];
            }
        }

        // Case: Self-registration
        if ($context->actor === 'self') {
            if ($context->flow === RegistrationFlow::USER_CREATES_ORG) {
                return [
                    $this->roleFactory->make('assign.admin.role'),
                    $this->permissionFactory->make('grant.admin.permissions'),
                ];
            }

            if ($context->flow === RegistrationFlow::USER_JOINS_USER_ORG) {
                return [
                    $this->roleFactory->make('assign.default.role'),
                    $this->permissionFactory->make('grant.default.permissions'),
                ];
            }
        }

        // Fallback
        return [
            $this->roleFactory->make('assign.default.role'),
            $this->permissionFactory->make('grant.default.permissions'),
        ];
    }
}
