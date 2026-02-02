# 🛡️ Guard - Modern Role & Permission Management for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/amdadulhaq/guard-laravel.svg?style=flat-square)](https://packagist.org/packages/amdadulhaq/guard-laravel)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/amdad121/guard-laravel/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/amdad121/guard-laravel/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/amdad121/guard-laravel/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/amdad121/guard-laravel/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/amdadulhaq/guard-laravel.svg?style=flat-square)](https://packagist.org/packages/amdadulhaq/guard-laravel)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-10%2F11%2F12-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)

> A powerful, flexible, and developer-friendly role and permission management system for Laravel applications.

## 🚀 Quick Start

Get up and running in 5 minutes:

> **Upgrading from an older version?** Check the [Upgrade Guide](UPGRADE.md) for detailed migration instructions.

```bash
# 1. Install via Composer
composer require amdadulhaq/guard-laravel

# 2. Publish and run migrations
php artisan vendor:publish --tag="guard-migrations"
php artisan migrate

# 3. Setup your User model
use AmdadulHaq\Guard\Contracts\User as UserContract;
use AmdadulHaq\Guard\Concerns\HasRoles;
use AmdadulHaq\Guard\Concerns\HasPermissions;

class User extends Authenticatable implements UserContract {
    use HasRoles;
    use HasPermissions;
}

# 4. Create your first role and permission
php artisan guard:create-role admin --label="Administrator"
php artisan guard:create-permission users.create --label="Create Users"

# 5. Protect your routes
Route::middleware('role:admin')->get('/admin', [AdminController::class, 'index']);
```

## ✨ Features

- 🎯 **Modern PHP & Laravel** - Built for PHP 8.2+ and Laravel 10/11/12
- 🔐 **Flexible Permission System** - Users can have permissions via roles AND directly assigned
- 🎭 **Wildcard Permissions** - Use `posts.*` to match all post-related permissions
- ⚡ **Smart Caching** - Automatic cache invalidation for optimal performance
- 🔑 **Laravel Gate Integration** - Native `@can`, `@canany`, `@cannot` support
- 🛡️ **Middleware Protection** - `role`, `permission`, and `role_or_permission` middleware
- 📦 **Type-Safe Enums** - IDE-friendly `PermissionType` and `CacheKey` enums
- 🏰 **Guarded Roles** - Protect critical roles from accidental deletion
- 📁 **Permission Groups** - Organize permissions by resource
- 🎨 **Interactive Commands** - Laravel Prompts for creating roles/permissions
- 🧹 **Clean Architecture** - Separated concerns with traits and contracts
- 🧪 **Developer Tools** - Pint, Pest, Rector, and Larastan included

## 📑 Table of Contents

- [Installation](#installation)
- [Upgrade Guide](UPGRADE.md)
- [Configuration](#configuration)
- [Usage](#usage)
    - [User Setup](#user-setup)
    - [Creating Roles](#creating-roles)
    - [Creating Permissions](#creating-permissions)
    - [Wildcard Permissions](#wildcard-permissions)
    - [Role Management](#role-management)
    - [Permission Management](#permission-management)
    - [Direct User Permissions](#direct-user-permissions)
    - [Checking Access](#checking-access)
    - [Middleware](#middleware)
    - [Gate Integration](#gate-integration)
    - [Blade Directives](#blade-directives)
    - [Artisan Commands](#artisan-commands)
    - [Query Scopes](#query-scopes)
- [Models Reference](#models-reference)
- [Exceptions](#exceptions)
- [Caching](#caching)
- [Database Structure](#database-structure)
- [Enums](#enums)
- [Development](#development)
- [Troubleshooting](#troubleshooting)
- [FAQ](#faq)

## 📦 Installation

### Requirements

- **PHP**: 8.2, 8.3, 8.4, or 8.5
- **Laravel**: 10.x, 11.x, or 12.x
- **Database**: MySQL 5.7+, PostgreSQL 9.6+, SQLite 3.8+, or SQL Server 2017+

### Step 1: Install via Composer

```bash
composer require amdadulhaq/guard-laravel
```

### Step 2: Publish Migrations

```bash
php artisan vendor:publish --tag="guard-migrations"
php artisan migrate
```

This creates 4 tables:

- `roles` - Role definitions
- `permissions` - Permission definitions
- `permission_role` - Role-permission relationships
- `role_user` - User-role relationships

### Step 3: Configure User Model

```php
<?php

namespace App\Models;

use AmdadulHaq\Guard\Contracts\User as UserContract;
use AmdadulHaq\Guard\Concerns\HasRoles;
use AmdadulHaq\Guard\Concerns\HasPermissions;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements UserContract
{
    use HasRoles;
    use HasPermissions;
}
```

### Step 4: (Optional) Publish Config

```bash
php artisan vendor:publish --tag="guard-config"
```

## ⚙️ Configuration

The `config/guard.php` file:

```php
return [
    'models' => [
        'user' => \App\Models\User::class,
        'role' => \AmdadulHaq\Guard\Models\Role::class,
        'permission' => \AmdadulHaq\Guard\Models\Permission::class,
    ],
    'tables' => [
        'roles' => 'roles',
        'permissions' => 'permissions',
    ],
    'cache' => [
        'enabled' => env('GUARD_CACHE_ENABLED', true),
        'roles_duration' => (int) env('GUARD_ROLES_CACHE_DURATION', 3600),
        'permissions_duration' => (int) env('GUARD_PERMISSIONS_CACHE_DURATION', 3600),
    ],
    'middleware' => [
        'role' => 'role',
        'permission' => 'permission',
        'role_or_permission' => 'role_or_permission',
    ],
    'wildcard' => [
        'enabled' => env('GUARD_WILDCARD_ENABLED', true),
    ],
];
```

## 🎯 Usage

### User Setup

Users implement the `UserContract` which combines `Roles` and `Permissions` contracts:

```php
use AmdadulHaq\Guard\Contracts\User as UserContract;
use AmdadulHaq\Guard\Concerns\HasRoles;
use AmdadulHaq\Guard\Concerns\HasPermissions;

class User extends Authenticatable implements UserContract
{
    use HasRoles;      // Role management methods
    use HasPermissions; // Permission management methods
}
```

### Creating Roles

```php
use AmdadulHaq\Guard\Models\Role;

// Create a role
$adminRole = Role::create([
    'name' => 'administrator',
    'label' => 'Administrator',
    'description' => 'Full system access',
    'is_guarded' => true, // Protected from deletion
]);

// Create via command
// php artisan guard:create-role moderator --label="Moderator"
```

**Role Model Methods:**

```php
$role->getName();              // Get role name
$role->isProtectedRole();      // Check if guarded
$role->getPermissionNames();   // Get all permission names
$role->users;                  // Get users with this role

// Query scopes
Role::guarded()->get();        // Only guarded roles
Role::unguarded()->get();      // Only unguarded roles
```

### Creating Permissions

```php
use AmdadulHaq\Guard\Models\Permission;

// Simple permission
Permission::create([
    'name' => 'users.create',
    'label' => 'Create Users',
    'description' => 'Can create new users',
    'group' => 'users', // For organization
]);

// Wildcard permission (auto-sets is_wildcard = true)
Permission::create([
    'name' => 'posts.*',
    'label' => 'Manage All Posts',
    'group' => 'posts',
]);

// Create via command
// php artisan guard:create-permission users.delete --label="Delete Users"
```

**Permission Model Methods:**

```php
$permission->getName();          // Get permission name
$permission->getLabel();         // Get human-readable label
$permission->getDescription();   // Get description
$permission->isWildcard();       // Check if wildcard (e.g., posts.*)
$permission->getGroup();         // Get group (e.g., 'users' from 'users.create')
$permission->getType();          // Get PermissionType enum (e.g., PermissionType::CREATE)
$permission->roles;              // Get roles with this permission

// Query scopes
Permission::wildcard()->get();           // Only wildcard permissions
Permission::byGroup('users')->get();     // Permissions in users group
```

### Wildcard Permissions

Wildcard permissions automatically match all sub-permissions:

```php
// Create wildcard permission
Permission::create(['name' => 'posts.*']);

// Assign to role
$role->givePermissionTo('posts.*');

// Now user can do all of these:
$user->hasPermission('posts.create');  // true
$user->hasPermission('posts.update');  // true
$user->hasPermission('posts.delete');  // true
$user->hasPermission('posts.publish'); // true
```

The `is_wildcard` boolean is automatically set when the name ends with `*`.

### Role Management

**Assigning Roles:**

```php
// Single role
$user->assignRole('administrator');
$user->assignRole($roleModel);

// Multiple roles
$user->assignRole(['administrator', 'editor']);

// Sync (replaces all)
$user->syncRoles(['administrator', 'editor']);
$user->syncRoles([$role1->id, $role2->id]);

// Sync without detaching existing
$user->syncRolesWithoutDetaching(['moderator']);

// Revoke
$user->revokeRole('editor');
$user->revokeRole($roleModel);
$user->revokeRoles(); // Revoke all
```

**Checking Roles:**

```php
// Single role
$user->hasRole('administrator');              // true/false

// Multiple roles
$user->hasAllRoles(['admin', 'editor']);     // Must have ALL
$user->hasAnyRole(['admin', 'moderator']);   // Must have ANY

// Get role names
$user->getRoleNames(); // ['administrator', 'editor']
```

### Permission Management

**Assigning to Roles:**

```php
// Single permission
$role->givePermissionTo('users.create');
$role->givePermissionTo($permissionModel);

// Multiple permissions
$role->givePermissionTo(['users.create', 'users.edit', 'users.delete']);

// Sync (replaces all)
$role->syncPermissions(['users.create', 'users.edit']);
$role->syncPermissions([$perm1->id, $perm2->id]);

// Revoke
$role->revokePermissionTo('users.delete');
$role->revokePermissionTo($permissionModel);
$role->revokeAllPermissions();
```

**Checking Role Permissions:**

```php
$role->hasPermissionTo('users.edit');    // Check if role has permission
$role->getPermissionNames();             // Get all permission names
```

### Direct User Permissions

Users can have permissions **directly** in addition to permissions from roles:

```php
// Give direct permission
$user->givePermissionTo('posts.delete');

// Multiple permissions
$user->givePermissionTo(['posts.create', 'posts.update']);

// Sync (replaces all direct permissions)
$user->syncPermissions(['posts.create', 'posts.update']);

// Revoke
$user->revokePermissionTo('posts.delete');
$user->revokeAllPermissions();
```

**Checking User Permissions:**

```php
// Check by name (checks roles + direct permissions)
$user->hasPermission('users.create');
$user->hasPermissionByName('users.edit');

// Check by model
$user->hasPermission($permissionModel);

// Wildcard matching
$user->hasPermission('posts.*');

// Get all permissions (roles + direct)
$user->getPermissions();
$user->getPermissionNames();
```

### Checking Access

**Role Checking:**

```php
if ($user->hasRole('administrator')) {
    // User has administrator role
}

if ($user->hasAllRoles(['admin', 'editor'])) {
    // User has both roles
}

if ($user->hasAnyRole(['admin', 'moderator'])) {
    // User has at least one role
}
```

**Permission Checking:**

```php
if ($user->hasPermission('users.create')) {
    // User can create users
}

if ($user->hasPermission('posts.*')) {
    // User has wildcard permission for posts
}
```

### Middleware

All middleware supports multiple values (requires ANY):

```php
// Role middleware
Route::middleware('role:administrator')->get('/admin', [AdminController::class, 'index']);

// Multiple roles (requires ANY)
Route::middleware('role:admin,editor')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});

// Permission middleware
Route::middleware('permission:users.create')->post('/users', [UserController::class, 'store']);

// Multiple permissions (requires ANY)
Route::middleware('permission:users.create,users.edit')->put('/users/{id}', [UserController::class, 'update']);

// Role OR permission middleware
Route::middleware('role_or_permission:admin,users.create')->get('/users', [UserController::class, 'index']);

// Multiple role_or_permission
Route::middleware('role_or_permission:admin,editor,posts.manage')->group(function () {
    Route::post('/manage', [Controller::class, 'handle']);
});
```

### Gate Integration

The package automatically registers Gates for all permissions and roles:

```php
// In controllers
public function store(Request $request)
{
    $this->authorize('users.create');
    // User can create users
}

// Using Gate facade
use Illuminate\Support\Facades\Gate;

if (Gate::allows('users.create')) {
    // Allowed
}

if (Gate::denies('users.delete')) {
    abort(403, 'Permission denied');
}

// Check for specific user
if (Gate::forUser($otherUser)->allows('posts.edit')) {
    // That user can edit posts
}

// Authorize roles
$this->authorize('administrator');
```

### Blade Directives

Guard provides custom Blade directives for role checking, in addition to Laravel's built-in `@can` directives:

**Custom Role Directives:**

```blade
@role('administrator')
    <div class="admin-panel">
        <h1>Admin Dashboard</h1>
    </div>
@endrole

@hasrole('editor')
    <p>Editor content here</p>
@endhasrole

@hasanyrole(['administrator', 'moderator'])
    <p>Content for admins or moderators</p>
@endhasanyrole

@hasallroles(['administrator', 'editor'])
    <p>Only for users with BOTH admin AND editor roles</p>
@endhasallroles
```

**Built-in Laravel Directives (via Gate integration):**

```blade
@can('users.create')
    <a href="/users/create">Create User</a>
@endcan

@canany(['users.create', 'users.edit'])
    <p>You can manage users</p>
@endcanany

@cannot('users.delete')
    <p>You cannot delete users</p>
@endcannot
```

### Artisan Commands

**Create a Role:**

```bash
php artisan guard:create-role admin --label="Administrator"

# With optional user assignment
php artisan guard:create-role moderator --label="Moderator" --user=1
```

**Create a Permission:**

```bash
php artisan guard:create-permission users.create --label="Create Users"

# With optional role assignment
php artisan guard:create-permission posts.delete --label="Delete Posts" --role=1
```

Both commands use interactive Laravel Prompts if arguments are not provided.

### Query Scopes

```php
// Users with specific role
User::whereHas('roles', function ($query) {
    $query->where('name', 'administrator');
})->get();

// Users with specific permission
User::whereHas('roles.permissions', function ($query) {
    $query->where('name', 'users.create');
})->get();

// Note: The traits have protected scopeWithRoles and scopeWithPermissions
// that can be used internally or extended in your User model
```

## 📚 Models Reference

### User Model (via Traits)

**HasRoles trait provides:**

- `roles()` - BelongsToMany relationship
- `assignRole($role)` - Assign single or multiple roles
- `syncRoles(array $roles, bool $detach = true)` - Sync roles
- `syncRolesWithoutDetaching(array $roles)` - Sync without detaching
- `revokeRole($role)` - Revoke specific role
- `revokeRoles()` - Revoke all roles
- `getRoleNames()` - Get all role names
- `hasRole($role)` - Check single role
- `hasAllRoles(...$roles)` - Check all roles
- `hasAnyRole(...$roles)` - Check any role

**HasPermissions trait provides:**

- `permissions()` - BelongsToMany relationship
- `givePermissionTo($permission)` - Give single or multiple permissions
- `syncPermissions(array $permissions)` - Sync permissions
- `revokePermissionTo($permission)` - Revoke specific permission
- `revokeAllPermissions()` - Revoke all permissions
- `getPermissionNames()` - Get all permission names
- `hasPermission($permission)` - Check permission (by name or model)
- `hasPermissionByName($name)` - Check by name
- `hasPermissionTo($permission)` - Check if has specific permission
- `getPermissions()` - Get all permissions (from roles + direct)

### Role Model

**Properties:**

- `name` (string, unique)
- `label` (string, nullable)
- `description` (text, nullable)
- `is_guarded` (boolean)

**Methods:**

- `getName()` - Get role name
- `isProtectedRole()` - Check if guarded
- `getPermissionNames()` - Get assigned permission names
- `permissions()` - BelongsToMany to permissions
- `users()` - BelongsToMany to users

**Scopes:**

- `guarded()` - Only guarded roles
- `unguarded()` - Only unguarded roles

### Permission Model

**Properties:**

- `name` (string, unique)
- `label` (string, nullable)
- `description` (text, nullable)
- `group` (string, nullable, indexed)
- `is_wildcard` (boolean, auto-set)

**Methods:**

- `getName()` - Get permission name
- `getLabel()` - Get human-readable label
- `getDescription()` - Get description
- `isWildcard()` - Check if wildcard pattern
- `getGroup()` - Get resource group (e.g., 'users')
- `getType()` - Get PermissionType enum
- `roles()` - BelongsToMany to roles
- `giveRoleTo($role)` - Give role to permission
- `syncRoles(array $roles)` - Sync roles
- `revokeRole($role)` - Revoke role
- `assignRole($role)` - Alias for giveRoleTo

**Scopes:**

- `wildcard()` - Only wildcard permissions
- `byGroup($group)` - Filter by group

## 🚨 Exceptions

```php
use AmdadulHaq\Guard\Exceptions\PermissionDeniedException;
use AmdadulHaq\Guard\Exceptions\RoleDoesNotExistException;
use AmdadulHaq\Guard\Exceptions\PermissionDoesNotExistException;

// Permission denied
throw PermissionDeniedException::create('users.delete');
throw PermissionDeniedException::roleNotAssigned('administrator');

// Role not found
throw RoleDoesNotExistException::named('admin');
throw RoleDoesNotExistException::withId(123);

// Permission not found
throw PermissionDoesNotExistException::named('users.delete');
throw PermissionDoesNotExistException::withId(456);
```

## 💾 Caching

The package uses intelligent caching:

```php
use AmdadulHaq\Guard\Facades\Guard;

// Clear cache manually
Guard::clearCache();
```

**Cache is automatically cleared when:**

- Roles or permissions are created/updated/deleted
- Role-permission relationships change
- User-role relationships change

**Configuration:**

```php
'cache' => [
    'enabled' => true,
    'roles_duration' => 3600,        // 1 hour
    'permissions_duration' => 3600,  // 1 hour
],
```

## 🗄️ Database Structure

### Roles Table

```php
Schema::create('roles', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique();
    $table->string('label')->nullable();
    $table->text('description')->nullable();
    $table->boolean('is_guarded')->default(false);
    $table->timestamps();
});
```

### Permissions Table

```php
Schema::create('permissions', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique();
    $table->string('label')->nullable();
    $table->text('description')->nullable();
    $table->string('group')->nullable()->index();
    $table->boolean('is_wildcard')->default(false);
    $table->timestamps();
});
```

### Permission-Role Pivot

```php
Schema::create('permission_role', function (Blueprint $table) {
    $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
    $table->foreignId('role_id')->constrained()->cascadeOnDelete();
    $table->primary(['permission_id', 'role_id']);
});
```

### Role-User Pivot

```php
Schema::create('role_user', function (Blueprint $table) {
    $table->foreignId('role_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->primary(['role_id', 'user_id']);
});
```

## 🔢 Enums

### PermissionType

```php
use AmdadulHaq\Guard\Enums\PermissionType;

PermissionType::CREATE->label();       // "Create"
PermissionType::READ->label();         // "Read"
PermissionType::WRITE->label();        // "Write"
PermissionType::UPDATE->label();       // "Update"
PermissionType::DELETE->label();       // "Delete"
PermissionType::VIEW_ANY->label();     // "View any"
PermissionType::VIEW->label();         // "View"
PermissionType::RESTORE->label();      // "Restore"
PermissionType::FORCE_DELETE->label(); // "Force delete"
PermissionType::MANAGE->label();       // "Manage"
```

### CacheKey

```php
use AmdadulHaq\Guard\Enums\CacheKey;

CacheKey::PERMISSIONS->value; // 'guard_permissions'
CacheKey::ROLES->value;       // 'guard_roles'
```

## 🛠️ Development

### Code Quality Tools

```bash
# Rector (code refactoring)
composer refactor
composer refactor:check

# Laravel Pint (code style)
composer lint
composer lint:check

# Pest (testing)
composer test
composer test-coverage

# Larastan (static analysis)
composer analyse
```

### Running Tests

```bash
# Run all tests
composer test

# With coverage
composer test-coverage
```

## 🔧 Troubleshooting

### Common Issues

**Issue: `Class 'AmdadulHaq\Guard\Concerns\HasRoles' not found`**

Solution:

```bash
composer dump-autoload
```

**Issue: `Target class [role] does not exist.`**

Solution:

```bash
php artisan config:clear
```

**Issue: Permissions not being recognized**

Solution:

```bash
php artisan cache:clear
# Or
php artisan tinker --execute="\AmdadulHaq\Guard\Facades\Guard::clearCache()"
```

### Performance Tips

1. **Keep caching enabled** in production
2. **Use wildcard permissions** to reduce permission count
3. **Filter at database level** instead of loading all users:

    ```php
    // ✅ Good
    User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->get();

    // ❌ Less efficient
    User::all()->filter(fn ($u) => $u->hasRole('admin'));
    ```

4. **Eager load** when needed:
    ```php
    User::with(['roles', 'roles.permissions'])->get();
    ```

## ❓ FAQ

**Q: Can I use this with Laravel Sanctum?**

A: Yes! Guard works seamlessly with Sanctum and any auth system.

**Q: Can users have permissions without roles?**

A: Yes! Users can have both role-based AND direct permissions using `givePermissionTo()`, `syncPermissions()`, etc.

**Q: How do wildcard permissions work?**

A: Create a permission like `posts.*` and it automatically matches `posts.create`, `posts.edit`, etc.

**Q: Can I customize table names?**

A: Yes, publish the config and modify the `tables` section.

**Q: Does it work with multiple guards?**

A: Yes, it integrates with Laravel's authorization system.

**Q: Is there a UI for managing roles?**

A: Guard is backend-only. For a UI, consider Filament Shield or build your own.

**Q: How do I create custom Blade directives?**

A: Use Laravel's built-in `@can`, `@canany`, `@cannot` directives which work automatically via Gate integration.

**Q: Can permissions be assigned to permissions?**

A: No, permissions are assigned to roles, and users get permissions via roles or direct assignment.

## 🤝 Contributing

We welcome contributions! Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## 📝 Changelog

See [CHANGELOG](CHANGELOG.md) for recent changes.

## 🔒 Security

Please review [our security policy](../../security/policy) for reporting vulnerabilities.

## 👏 Credits

![Contributors](https://contrib.rocks/image?repo=amdad121/guard-laravel)

## 📄 License

The MIT License (MIT). See [License File](LICENSE.md) for details.

---

<p align="center">Made with ❤️ for the Laravel community</p>
