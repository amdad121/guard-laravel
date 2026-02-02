# Upgrade Guide

Current Version: **v1.3.0**

## Quick Upgrade

1. **Update the package**

   ```bash
   composer update amdadulhaq/guard-laravel
   ```

2. **Update your User model**

   ```bash
   # Your User model now needs to use both traits:
   # HasRoles and HasPermissions
   ```

3. **Publish new migrations** (if using custom models)

   ```bash
   php artisan vendor:publish --tag="guard-migrations" --force
   php artisan migrate
   ```

4. **Update config** (if using custom models or table names)

   ```bash
   php artisan vendor:publish --tag="guard-config" --force
   ```

5. **Clear cache**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   ```

## Breaking Changes

### Contracts Renamed

- `HasRoles` contract → now called **`Roles`**
- `HasPermissions` contract → now called **`Permissions`**
- Old import: `use AmdadulHaq\Guard\HasRoles;`
- New import: `use AmdadulHaq\Guard\Concerns\HasRoles;`

### Traits Moved to Concerns Directory

- All traits moved from `src/` to `src/Concerns/` directory
- `HasRoles` trait moved to `src/Concerns/HasRoles.php`
- `HasPermissions` trait moved to `src/Concerns/HasPermissions.php`
- Old import: `use AmdadulHaq\Guard\HasRoles;`
- New import: `use AmdadulHaq\Guard\Concerns\HasRoles;`

### Middleware Support for Multiple Roles/Permissions

All middlewares now support multiple roles/permissions via variadic parameters:

**Old usage:**

```php
Route::middleware('role:admin')->group(function () {
    // ...
});
```

**New usage:**

```php
// Single role
Route::middleware('role:admin')->group(function () {
    // ...
});

// Multiple roles (comma-separated or multiple parameters)
Route::middleware('role:admin,editor')->group(function () {
    // ...
});

Route::middleware('role', 'admin', 'editor')->group(function () {
    // ...
});
```

Same applies to `permission` and `role_or_permission` middlewares.

### Shared Concerns Added

Two new shared traits created for DRY principle:

1. **ChecksRoles** - Shared role checking logic (`hasRole`, `hasAllRoles`, `hasAnyRole`)
2. **ResolvesModels** - Shared model resolution from strings

These are automatically used by all implementing classes.

### Model Resolution Methods

All methods that accept role/permission by name now have better type hints:

- `assignRole()` - Now accepts `Model|string`
- `revokeRole()` - Now accepts `Model|string`
- `givePermissionTo()` - Now accepts `Model|string`
- `revokePermissionTo()` - Now accepts `Model|string`
- `syncRoles()` - Now accepts `array<int, int|string>`
- `syncPermissions()` - Now accepts `array<int, int|string>`

## Migration Guide

### Step 1: Update User Model Imports

**Old code:**

```php
use AmdadulHaq\Guard\HasRoles;
use AmdadulHaq\Guard\HasPermissions;

class User extends Authenticatable implements UserContract
{
    use HasRoles;
    use HasPermissions;
}
```

**New code:**

```php
use AmdadulHaq\Guard\Concerns\HasRoles;
use AmdadulHaq\Guard\Concerns\HasPermissions;

class User extends Authenticatable implements UserContract
{
    use HasRoles;
    use HasPermissions;
}
```

### Step 2: Update Middleware Usage (Optional)

If you're using multiple roles in middleware, update to comma-separated format:

**Old code:**

```php
Route::middleware(['role:admin', 'role:editor'])->group(function () {
    // ...
});
```

**New code:**

```php
Route::middleware('role:admin,editor')->group(function () {
    // ...
});
```

### Step 3: Update Config (Optional)

If you're using custom role or permission model classes, update your config:

```php
'models' => [
    'user' => \App\Models\User::class,
    'role' => \App\Models\CustomRole::class, // Update if needed
    'permission' => \App\Models\CustomPermission::class, // Update if needed
],
```

### Step 4: Verify Installation

Run tests to ensure everything works:

```bash
composer test
```

## Method Signature Changes

### All Methods That Accept Models

All methods now accept both model instances and string names:

| Method                 | Old Signature                           | New Signature                   |
| ---------------------- | --------------------------------------- | ------------------------------- | ---------------------- |
| `assignRole()`         | `assignRole(Model $role)`               | `assignRole(Model               | string $role)`         |
| `revokeRole()`         | `revokeRole(Model $role)`               | `revokeRole(Model               | string $role)`         |
| `givePermissionTo()`   | `givePermissionTo(Model $permission)`   | `givePermissionTo(Model         | string $permission)`   |
| `revokePermissionTo()` | `revokePermissionTo(Model $permission)` | `revokePermissionTo(Model       | string $permission)`   |
| `syncRoles()`          | `syncRoles(array $roleIds)`             | `syncRoles(array<int, int       | string> $roles)`       |
| `syncPermissions()`    | `syncPermissions(array $permissionIds)` | `syncPermissions(array<int, int | string> $permissions)` |

### Returns Type Updates

Some methods now return self for fluent chaining:

| Method               | Old Return | New Return |
| -------------------- | ---------- | ---------- |
| `assignRole()`       | `Model`    | `self`     |
| `givePermissionTo()` | `Model`    | `self`     |
| `syncRoles()`        | `array`    | `array`    |
| `syncPermissions()`  | `array`    | `array`    |

## Full Feature List

All existing functionality is preserved:

- ✅ Role & Permission Management
- ✅ Wildcard Permissions
- ✅ Permission Groups
- ✅ Guarded Roles
- ✅ Custom Middleware (with multiple support)
- ✅ Cache Support
- ✅ Query Scopes
- ✅ Custom Exceptions
- ✅ Enums
- ✅ Developer Tools
- ✅ 42 tests passing

## That's It!

Your application should work immediately after these steps. All existing functionality remains intact.

## Need Help?

If you encounter any issues:

1. Check the [CHANGELOG](CHANGELOG.md) for changes
2. Clear all caches: `php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear`
3. Run tests: `composer test`
4. Open an issue on [GitHub](https://github.com/amdad121/guard-laravel/issues)
