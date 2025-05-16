<?php

namespace App\Services\Permissions;

class PermissionNameResolver
{
    /**
     * Resolve permission name from permission and organization slug
     *
     * @param string $permission Permission name
     * @param string $organizationSlug Organization slug
     * @param string $action Optional action (e.g., 'view', 'edit', 'delete')
     * @return string
     */
    public function resolve(string $permission, string $organizationSlug, ?string $action = null): string
    {
        $name = "{$organizationSlug}:{$permission}";

        if ($action) {
            $name .= ":{$action}";
        }

        return $name;
    }

    /**
     * Get an array of all permission names for a permission in an organization
     *
     * @param string $permission Permission name
     * @param string $organizationSlug Organization slug
     * @param array $actions List of actions
     * @return array
     */
    public function resolveAll(string $permission, string $organizationSlug, array $actions = []): array
    {
        if (empty($actions)) {
            return [$this->resolve($permission, $organizationSlug)];
        }

        return array_map(function ($action) use ($permission, $organizationSlug) {
            return $this->resolve($permission, $organizationSlug, $action);
        }, $actions);
    }
}
