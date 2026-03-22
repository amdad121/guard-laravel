# Upgrade Guide

Current Version: **v1.4.0**

## Quick Upgrade

### Upgrading to v1.4.0 (Latest)

1. **Update the package**

    ```bash
    composer update amdadulhaq/guard-laravel
    ```

2. **Clear cache**

    ```bash
    php artisan cache:clear
    php artisan config:clear
    php artisan view:clear
    ```

3. **Review release-specific changes**

    If you're already on `v1.3.x`, `v1.4.0` is a documentation-only release and does not require application code changes.

    If you're upgrading from `v1.2.x` or earlier, review the `v1.2.1` Blade directives and `v1.3.0` Laravel 13 support updates below.

### Upgrading to v1.3.0

1. **Update the package**

    ```bash
    composer update amdadulhaq/guard-laravel
    ```

2. **Laravel 13 support**

    `v1.3.0` adds Laravel 13 support. No package API changes are required, but confirm your application and dependency set are compatible before upgrading.

### Upgrading to v1.2.1

1. **Update the package**

    ```bash
    composer update amdadulhaq/guard-laravel
    ```

2. **New Feature: Blade Directives** (Optional)

    `v1.2.1` introduces custom Blade directives for role checking. No code changes are required; they are automatically available:

    ```blade
    @role('administrator')
        <div>Admin content</div>
    @endrole

    @hasanyrole(['admin', 'editor'])
        <div>Admin or Editor content</div>
    @endhasanyrole
    ```

### Upgrading from v1.2.x to v1.3.0+

1. **Update the package**

    ```bash
    composer update amdadulhaq/guard-laravel
    ```

2. **Update your User model**

    Choose the setup that matches your app:

    - Roles only: implement `AmdadulHaq\Guard\Contracts\Roles` and use `HasRoles`
    - Roles + permissions: implement `AmdadulHaq\Guard\Contracts\User` and use `HasRoles` + `HasPermissions`

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
- `User` remains the user-side contract for permission checks
- `Permissions` is the direct permission-management contract for role-like models
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

### Role and Permission Input Methods

Methods that accept roles or permissions now support model instances, names, and where documented IDs:

- `assignRole()` - Now accepts `Model|string|int|array` and variadic input
- `revokeRole()` - Now accepts `Model|string`
- `givePermissionTo()` - Now accepts `Model|string|int|array` and variadic input
- `revokePermissionTo()` - Now accepts `Model|string`
- `syncRoles()` - Now accepts `array<int, int|string>`
- `syncPermissions()` - Now accepts `array<int, int|string>`

## Migration Guide

### Step 1: Update User Model Imports

**Roles only:**

```php
use AmdadulHaq\Guard\HasRoles;
use AmdadulHaq\Guard\Contracts\Roles as RolesContract;

class User extends Authenticatable implements RolesContract
{
    use HasRoles;
}
```

**Roles + Permissions:**

```php
use AmdadulHaq\Guard\Concerns\HasRoles;
use AmdadulHaq\Guard\Concerns\HasPermissions;
use AmdadulHaq\Guard\Contracts\User as UserContract;

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

All methods now accept model instances, names, and where documented IDs:

| Method                 | Old Signature                           | New Signature                                               |
| ---------------------- | --------------------------------------- | ----------------------------------------------------------- |
| `assignRole()`         | `assignRole(Model $role)`               | `assignRole(Model\|string\|int\|array ...$roles)`           |
| `revokeRole()`         | `revokeRole(Model $role)`               | `revokeRole(Model\|string $role)`                           |
| `givePermissionTo()`   | `givePermissionTo(Model $permission)`   | `givePermissionTo(Model\|string\|int\|array ...$permissions)` |
| `revokePermissionTo()` | `revokePermissionTo(Model $permission)` | `revokePermissionTo(Model\|string $permission)`             |
| `syncRoles()`          | `syncRoles(array $roleIds)`             | `syncRoles(array $roles)` supports IDs or names             |
| `syncPermissions()`    | `syncPermissions(array $permissionIds)` | `syncPermissions(array $permissions)` supports IDs or names |

### Returns Type Updates

Some methods now return self for fluent chaining:

| Method               | Old Return | New Return |
| -------------------- | ---------- | ---------- |
| `assignRole()`       | `Model`    | `self`     |
| `givePermissionTo()` | `Model`    | `Model`    |
| `syncRoles()`        | `array`    | `array`    |
| `syncPermissions()`  | `array`    | `array`    |

## Full Feature List

All existing functionality is preserved:

- ✅ Role & Permission Management
- ✅ Wildcard Permissions
- ✅ Permission Groups
- ✅ Guarded Roles
- ✅ Custom Middleware (with multiple support)
- ✅ **Custom Blade Directives (NEW in v1.2.1)**
    - `@role('admin')` - Check single role
    - `@hasrole('admin')` - Alternative syntax
    - `@hasanyrole(['admin', 'editor'])` - Check any role
    - `@hasallroles(['admin', 'editor'])` - Check all roles
- ✅ Cache Support
- ✅ Query Scopes
- ✅ Custom Exceptions
- ✅ Enums
- ✅ Developer Tools
- ✅ Roles-only usage or roles plus permissions

## That's It!

Your application should work immediately after these steps. All existing functionality remains intact.

## Need Help?

If you encounter any issues:

1. Check the [CHANGELOG](CHANGELOG.md) for changes
2. Clear all caches: `php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear`
3. Run tests: `composer test`
4. Open an issue on [GitHub](https://github.com/amdad121/guard-laravel/issues)
