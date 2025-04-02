````markdown
# Permissions and Access Control

## Structure

Uses `spatie/laravel-permission` with extensions.

---

## Dynamic Permission Naming

```bash
org-{organizationId}-{resource}.{action}
```
````

Example:

```bash
org-123-user.view
org-123-profile.update
```

---

## Policies

Policies validate organization-based access:

```php
public function view(User $user, User $target)
{
    return $user->hasPermissionTo("org-{$target->organization_id}-user.view");
}
```

Policies auto-resolve from dynamic paths.

---

## Role Assignment

Roles and permissions are synced using:

```bash
php artisan iam:sync-permissions
```

```

```
