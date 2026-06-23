# Upgrade Guide

Current Version: **v2.0.0**

## Quick Upgrade

### Upgrading to v2.0.0 (Latest)

The `v2.0.0` release introduces strict architectural optimizations and removes all legacy backward compatibility layers.

1. **Update the package**

    ```bash
    composer update amdadulhaq/guard-laravel
    ```

2. **Run the Automatic Upgrade Command (Recommended)**

    We have provided an Artisan command to automatically refactor your application's models (`app/Models`) to the new architecture. Run:

    ```bash
    php artisan guard:upgrade
    ```

3. **Or Apply Manually (If not using the command)**

    The `HasPermissions` trait and `Roles` contract were merged into the `Roleable` trait and the `Roleable` contract respectively. 
    You should:
    - Remove `use HasPermissions;` and its import from your `User` model.
    - Change implementation of `Roles` contract to the unified `Roleable` contract (aliased as `RoleableContract`).
    - Change use of `HasRoles` trait to `Roleable` trait.

    ```diff
    - use AmdadulHaq\Guard\Concerns\HasPermissions;
    - use AmdadulHaq\Guard\Concerns\HasRoles;
    + use AmdadulHaq\Guard\Concerns\Roleable;
    - use AmdadulHaq\Guard\Contracts\Roles as RolesContract;
    + use AmdadulHaq\Guard\Contracts\Roleable as RoleableContract;

    - class User extends Authenticatable implements RolesContract
    + class User extends Authenticatable implements RoleableContract
      {
    -     use HasPermissions;
    -     use HasRoles;
    +     use Roleable;
      }
    ```

    Also note: The return type for `assignRole()` in the `Roleable` contract was updated from `Model` to `self` to support fluent chaining. Ensure your implementations match this if you have heavily customized the trait.

2. **Removed Exceptions**

    To simplify error handling, the following redundant exception classes have been removed:
    - `GuardException`
    - `RoleDoesNotExistException`
    - `PermissionDoesNotExistException`

    **Action:** If you were catching `RoleDoesNotExistException` or `PermissionDoesNotExistException`, update your code to catch standard Eloquent `ModelNotFoundException` where applicable. For authorization checks, continue catching `PermissionDeniedException`.

3. **Console Commands Changes**

    Manual prompt fallbacks for missing arguments in artisan commands (`guard:create-role` and `guard:create-permission`) have been removed. The commands now strictly rely on Laravel's native `PromptsForMissingInput`. If you are programmatically calling these commands via tests or code without all required arguments, ensure you provide them directly or update your test expectations.

5. **Clear Caches**

    After making code changes, clear your application caches:

    ```bash
    php artisan cache:clear
    php artisan config:clear
    php artisan view:clear
    ```



### Upgrading to v1.3.0

1. **Update the package**

    ```bash
    composer update amdadulhaq/guard-laravel
    ```

2. **Laravel 13 support**

    `v1.3.0` adds Laravel 13 support. No package API changes are required, but confirm your application and dependency set are compatible before upgrading.

---

### Upgrading to v1.2.1

1. **Update the package**

    ```bash
    composer update amdadulhaq/guard-laravel
    ```

2. **New Feature: Blade Directives** (Optional)

    `v1.2.1` introduced custom Blade directives for role checking. No code changes are required; they are automatically available:

    ```blade
    @role('administrator')
        <div>Admin content</div>
    @endrole

    @hasanyrole(['admin', 'editor'])
        <div>Admin or Editor content</div>
    @endhasanyrole
    ```

---

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

## Breaking Changes v1.x

### Contracts Renamed
- `HasRoles` contract → now called **`Roles`**
- `User` remains the user-side contract for permission checks
- `Permissions` is the direct permission-management contract for role-like models

### Traits Moved to Concerns Directory
- All traits moved from `src/` to `src/Concerns/` directory

### Middleware Support for Multiple Roles/Permissions
All middlewares now support multiple roles/permissions via variadic parameters:

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

## Need Help?

If you encounter any issues:

1. Check the [CHANGELOG](CHANGELOG.md) for changes
2. Clear all caches: `php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear`
3. Run tests: `composer test`
4. Open an issue on [GitHub](https://github.com/amdad121/guard-laravel/issues)
