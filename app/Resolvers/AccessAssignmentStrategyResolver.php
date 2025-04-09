<?php

namespace App\Resolvers;

use App\Contracts\Access\RoleAssignmentStrategy;
use App\Contracts\Access\PermissionAssignmentStrategy;
use App\Data\AccessAssignmentData;
use App\Factories\RoleAssignmentStrategyFactory;
use App\Factories\PermissionAssignmentStrategyFactory;
use App\Enums\RegistrationFlow;
use Kwidoo\Mere\Contracts\Lifecycle;

class AccessAssignmentStrategyResolver
{
    public function __construct(
        protected RoleAssignmentStrategyFactory $roleFactory,
        protected PermissionAssignmentStrategyFactory $permissionFactory,
    ) {}

    /**
     * @return array{RoleAssignmentStrategy, PermissionAssignmentStrategy}
     */
    public function resolve(AccessAssignmentData $context, Lifecycle $lifecycle): array
    {
        if (in_array($context->actor, ['admin', 'super_admin'])) {
            if ($context->flow === RegistrationFlow::USER_CREATES_ORG) {
                return [
                    $this->roleFactory->make('assign.admin.role', $lifecycle),
                    $this->permissionFactory->make('grant.admin.permissions', $lifecycle),
                ];
            }

            if ($context->flow === RegistrationFlow::USER_JOINS_USER_ORG) {
                return [
                    $this->roleFactory->make('assign.default.role', $lifecycle),
                    $this->permissionFactory->make('grant.default.permissions', $lifecycle),
                ];
            }
        }

        // Case: Self-registration
        if ($context->actor === 'self') {
            if ($context->flow === RegistrationFlow::USER_CREATES_ORG) {
                return [
                    $this->roleFactory->make('assign.admin.role', $lifecycle),
                    $this->permissionFactory->make('grant.admin.permissions', $lifecycle),
                ];
            }

            if ($context->flow === RegistrationFlow::USER_JOINS_USER_ORG) {
                return [
                    $this->roleFactory->make('assign.default.role', $lifecycle),
                    $this->permissionFactory->make('grant.default.permissions', $lifecycle),
                ];
            }
        }

        // Fallback
        return [
            $this->roleFactory->make('assign.default.role', $lifecycle),
            $this->permissionFactory->make('grant.default.permissions', $lifecycle),
        ];
    }
}
