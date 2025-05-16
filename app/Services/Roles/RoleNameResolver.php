<?php

namespace App\Services\Roles;

class RoleNameResolver
{
    /**
     * Resolve role name from user role and organization slug
     *
     * @param string $role User role
     * @param string $organizationSlug Organization slug
     * @param string $action Optional action (e.g., 'view', 'edit', 'delete')
     * @return string
     */
    public function resolve(string $role, string $organizationSlug, ?string $action = null): string
    {
        $name = "{$organizationSlug}:{$role}";

        if ($action) {
            $name .= ":{$action}";
        }

        return $name;
    }

    /**
     * Get an array of all role names for a role in an organization
     *
     * @param string $role User role
     * @param string $organizationSlug Organization slug
     * @param array $actions List of actions
     * @return array
     */
    public function resolveAll(string $role, string $organizationSlug, array $actions = []): array
    {
        if (empty($actions)) {
            return [$this->resolve($role, $organizationSlug)];
        }

        return array_map(function ($action) use ($role, $organizationSlug) {
            return $this->resolve($role, $organizationSlug, $action);
        }, $actions);
    }
}
