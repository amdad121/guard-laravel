# Upgrade Guide

Current Version: **v1.1.0**

## Quick Upgrade

1. **Update the package**

   ```bash
   composer update amdadulhaq/guard-laravel
   ```

2. **Publish new migrations**

   ```bash
   php artisan vendor:publish --tag="guard-migrations" --force
   php artisan migrate
   ```

3. **Update config (optional)**

   ```bash
   php artisan vendor:publish --tag="guard-config" --force
   ```

4. **Clear cache**
   ```bash
   php artisan cache:clear
   ```

## Breaking Changes

### HasRoles Trait

- `assignRole()` now returns `self` instead of `Model`
- `revokeRole()` signature changed to accept `RoleContract|Model|string`
- New methods added: `syncRolesWithoutDetaching()`, `revokeRoles()`, `getRoleNames()`
- Improved `hasRole()`, `hasAllRoles()`, `hasAnyRole()` to handle Collection types

### HasPermissions Trait

- New method added: `revokeAllPermissions()`
- New method added: `hasPermissionTo()`
- New helper method: `getPermissionIdByName()`

### Models

- Revoked `static $table` pattern from `Permission` and `Role` models

## Migration Guide

### If you have custom User model

Update your User model to use the trait properly:

```php
use AmdadulHaq\Guard\Contracts\User as UserContract;
use AmdadulHaq\Guard\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements UserContract
{
    use HasRoles; // Now returns self for fluent chaining

    public function assignRole($role)
    {
        // Method signature unchanged
    }

    // Ensure revokeRoles() method is added if needed
    public function revokeRoles()
    {
        return $this->roles()->detach();
    }
}
```

### Update your code

If you were using the old `detach()` in `syncRoles()`:

```php
// Old code (will still work)
$user->syncRoles([$role1->id, $role2->id]);

// Use new syncRolesWithoutDetaching() instead of syncRoles() for partial sync
$user->syncRolesWithoutDetaching(['editor', 'moderator']);
```

## That's It!

Your application should work immediately after these steps. All existing functionality remains intact.

## Need Help?

If you encounter any issues:

1. Check the [CHANGELOG](CHANGELOG.md) for changes
2. Clear all caches: `php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear`
3. Open an issue on [GitHub](https://github.com/amdad121/guard-laravel/issues)
