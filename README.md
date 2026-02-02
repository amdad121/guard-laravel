# Guard Role And Permission Package For Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/amdadulhaq/guard-laravel.svg?style=flat-square)](https://packagist.org/packages/amdadulhaq/guard-laravel)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/amdad121/guard-laravel/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/amdad121/guard-laravel/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/amdad121/guard-laravel/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/amdad121/guard-laravel/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/amdadulhaq/guard-laravel.svg?style=flat-square)](https://packagist.org/packages/amdadulhaq/guard-laravel)

Guard is a modern Role and Permission management system for Laravel 10, 11, and 12 with PHP 8.2, 8.3, 8.4, and 8.5 support.

## Sponsor This Project

If you find Guard Laravel helpful, please consider **sponsoring** the project. Your support helps maintain and improve the package.

**[Become a GitHub Sponsor](https://github.com/sponsors/amdad121)** - Any amount is greatly appreciated!

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Upgrade Guide](#upgrade-guide)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Creating Roles](#creating-roles)
  - [Creating Permissions](#creating-permissions)
  - [Wildcard Permissions](#wildcard-permissions)
  - [Assigning Permissions to Roles](#assigning-permissions-to-roles)
  - [Assigning Roles to Users](#assigning-roles-to-users)
  - [Direct User Permissions](#direct-user-permissions)
  - [Checking Roles](#checking-roles)
  - [Checking Permissions](#checking-permissions)
  - [Advanced Permission Checking](#advanced-permission-checking)
  - [Query Scopes](#query-scopes)
  - [Middleware](#middleware)
  - [Gate Integration](#gate-integration)
  - [Blade Directives](#blade-directives)
  - [Permission Helpers](#permission-helpers)
  - [Role Helpers](#role-helpers)
  - [Cache Management](#cache-management)
  - [Custom Exceptions](#custom-exceptions)
  - [Seeding Roles and Permissions](#seeding-roles-and-permissions)
  - [API Protection](#api-protection)
- [Database Structure](#database-structure)
- [Available Enums](#available-enums)
- [Development Tools](#development-tools)
- [Testing](#testing)
- [Contributing](#contributing)
- [Changelog](#changelog)
- [Security Vulnerabilities](#security-vulnerabilities)
- [Credits](#credits)
- [License](#license)

## Features

- **PHP 8.2, 8.3, 8.4, & 8.5 & Laravel 10, 11, & 12** - Modern PHP and Laravel features
- **Role & Permission Management** - Create and manage roles with permissions
- **Separate Concerns** - Traits and contracts are properly separated for better maintainability
- **Wildcard Permissions** - Use wildcard patterns like `posts.*` for flexible permission checking
- **Permission Groups** - Organize permissions into groups
- **Guarded Roles** - Protect certain roles from deletion/modification
- **Multiple Roles/Permissions** - Middleware now supports multiple roles/permissions at once
- **Cache Support** - Intelligent caching with automatic invalidation
- **Query Scopes** - Filter users by roles and permissions
- **Custom Exceptions** - Better error messages
- **Enums** - Type-safe constants for CacheKey and PermissionType
- **Developer Tools** - Includes Pint, Pest, Rector, and Larastan

## Requirements

- **PHP**: 8.2, 8.3, 8.4, or 8.5 or higher
- **Laravel**: 10, 11, or 12
- **Database**: MySQL, PostgreSQL, SQLite, or SQL Server

## Installation

You can install the package via composer:

```bash
composer require amdadulhaq/guard-laravel
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="guard-migrations"
php artisan migrate
```

Add the `HasRoles` trait and `User` interface to your User model:

```php
namespace App\Models;

use AmdadulHaq\Guard\Contracts\User as UserContract;
use AmdadulHaq\Guard\Concerns\HasRoles;
use AmdadulHaq\Guard\Concerns\HasPermissions;

class User extends Authenticatable implements UserContract
{
    use HasRoles;
    use HasPermissions;
}
```

---

## Upgrade Guide

If you're upgrading from an older version, please follow the [Upgrade Guide](UPGRADE.md) for detailed instructions.

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag="guard-config"
```

Configuration options:

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

## Usage

### Creating Roles

```php
use AmdadulHaq\Guard\Models\Role;

$role = Role::create([
    'name' => 'administrator',
    'label' => 'Administrator',
    'description' => 'Full system access',
    'is_guarded' => true,
]);
```

### Creating Permissions

```php
use AmdadulHaq\Guard\Models\Permission;
use AmdadulHaq\Guard\Models\Role;

$permission = Permission::create([
    'name' => 'users.create',
    'label' => 'Create Users',
    'description' => 'Create new user accounts',
    'group' => 'users',
]);

$role->givePermissionTo($permission);
```

### Wildcard Permissions

Create wildcard permissions for easier management:

```php
Permission::create(['name' => 'posts.*', 'label' => 'All Post Permissions']);

// A user with this role can:
$user->hasPermission('posts.create'); // true
$user->hasPermission('posts.update'); // true
$user->hasPermission('posts.delete'); // true
```

### Assigning Permissions to Roles

```php
use AmdadulHaq\Guard\Models\Permission;

// Assign a single permission by model
$role->givePermissionTo($permission);

// Assign a single permission by name
$role->givePermissionTo('users.create');

// Sync multiple permissions (supports both IDs and names)
$role->syncPermissions([$permission1->id, $permission2->id]);
$role->syncPermissions(['users.create', 'users.edit', 'users.delete']);

// Sync without detaching existing permissions
$role->syncRolesWithoutDetaching(['editor', 'moderator']);

// Revoke a specific permission
$role->revokePermissionTo($permission);
$role->revokePermissionTo('users.delete');

// Revoke all permissions
$role->revokeAllPermissions();

// Check if role has a permission
$role->hasPermissionTo('users.edit'); // true or false
```

### Assigning Roles to Users

```php
use App\Models\User;

$user = User::first();

// Assign by model
$user->assignRole($role);

// Assign by name
$user->assignRole('administrator');

// Sync multiple roles
$user->syncRoles([$role1->id, $role2->id]);

// Sync without detaching existing roles
$user->syncRolesWithoutDetaching(['editor', 'moderator']);

// Revoke a specific role
$user->revokeRole($role);
$user->revokeRole('editor');

// Revoke all roles
$user->revokeRoles();

// Get all role names
$user->getRoleNames(); // ['administrator', 'editor']

// Check if model has all specified roles
$user->hasAllRoles(['admin', 'editor']); // true if user has both

// Check if model has any of the specified roles
$user->hasAnyRole(['admin', 'editor']); // true if user has at least one
```

### Direct User Permissions

Assign permissions directly to users without going through roles:

```php
use App\Models\User;

$user = User::first();

// Give permission directly to user
$user->givePermissionTo('posts.delete');

// Give multiple permissions
$user->givePermissionTo(['posts.create', 'posts.update']);

// Sync user permissions (replaces all existing permissions)
$user->syncPermissions(['posts.create', 'posts.update', 'posts.delete']);

// Revoke specific permission
$user->revokePermissionTo('posts.delete');

// Revoke all permissions
$user->revokeAllPermissions();

// Check if user has direct permission
$user->hasPermission('posts.create'); // true

// Get all permissions (roles + direct)
$user->getPermissions(); // Collection of all permissions
```

### Checking Roles

```php
$user->hasRole('administrator'); // true or false
$user->hasAllRoles(['admin', 'editor']); // true if user has all
$user->hasAnyRole(['admin', 'editor']); // true if user has any
```

### Checking Permissions

```php
$user->hasPermission('users.create'); // Check by name
$user->hasPermission($permissionModel); // Check by model

// Get all permissions from user's roles
$allPermissions = $user->getPermissions();
```

### Advanced Permission Checking

Check multiple permissions at once with different logic:

```php
// Check if user has ALL specified permissions
$user->hasAllPermissions(['users.create', 'users.edit']); // true only if user has both

// Check if user has ANY of the specified permissions
$user->hasAnyPermission(['users.delete', 'users.edit']); // true if user has at least one

// Check using permission models
$permission1 = Permission::where('name', 'users.create')->first();
$permission2 = Permission::where('name', 'users.edit')->first();
$user->hasAllPermissions([$permission1, $permission2]);

// Check permissions via wildcard
$user->hasPermission('posts.*'); // true if user has posts.create, posts.edit, etc.

// Get all role names for a user
$user->getRoleNames(); // ['administrator', 'editor']

// Get permission names grouped by role
$permissionsByRole = $user->getPermissionsByRole();
// [
//     'administrator' => ['users.create', 'users.delete'],
//     'editor' => ['posts.create', 'posts.edit']
// ]
```

### Query Scopes

Filter users by roles and permissions:

```php
// Get users with specific role
User::withRoles('administrator')->get();

// Get users with specific permission
User::withPermissions('users.create')->get();
```

### Middleware

Protect routes using built-in middleware. All middlewares support multiple roles/permissions:

```php
// Single permission
Route::middleware('permission:users.create')->group(function () {
    Route::post('/users', [UserController::class, 'store']);
});

// Multiple permissions (requires any of them)
Route::middleware('permission:users.create,users.edit')->group(function () {
    Route::put('/users/{id}', [UserController::class, 'update']);
});

// Single role
Route::middleware('role:administrator')->group(function () {
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});

// Multiple roles (requires any of them)
Route::middleware('role:admin,editor')->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard']);
});

// Role OR permission
Route::middleware('role_or_permission:admin,users.create')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
});

// Multiple role_or_permission
Route::middleware('role_or_permission:admin,editor,users.manage')->group(function () {
    Route::post('/manage', [Controller::class, 'handle']);
});
```

### Gate Integration

Use Laravel's Gate and authorization:

```php
// Using Gate facade
Gate::authorize('users.create');
Gate::authorize('administrator');

// Using controller helper
$this->authorize('users.create');

// Check via Gate facade
if (Gate::allows('users.create')) {
    // User can create users
}

if (Gate::denies('users.delete')) {
    abort(403);
}

// Using Gate::forUser for a specific user
if (Gate::forUser($user)->allows('posts.edit')) {
    // This specific user can edit posts
}
```

### Blade Directives

Use Blade directives for conditional rendering in your views:

```php
@can('users.create')
    <button>Create User</button>
@elsecan('users.edit')
    <button>Edit Users</button>
@else
    <p>No permission</p>
@endcan

@canany(['users.create', 'users.edit'])
    <p>You can create or edit users</p>
@endcanany

@cannot('users.delete')
    <p>You cannot delete users</p>
@endcannot

@role('administrator')
    <p>Admin content - only visible to administrators</p>
@endrole

@hasrole('administrator')
    <p>Alternative syntax for role check</p>
@endhasrole

@hasanyrole(['administrator', 'editor'])
    <p>Visible to admins and editors</p>
@endhasanyrole

@hasallroles(['administrator', 'moderator'])
    <p>Visible only to users with both roles</p>
@endhasallroles
```

### Permission Helpers

Get permission information:

```php
$permission->isWildcard(); // Check if permission is wildcard
$permission->getGroup(); // Get permission group (e.g., 'users')
$permission->getType(); // Get permission type enum
```

### Role Helpers

```php
$role->isProtectedRole(); // Check if role is protected
$role->getPermissionNames(); // Get all permission names for role

// Query scopes
Role::guarded()->get(); // Get all guarded roles
Role::unguarded()->get(); // Get all unguarded roles
```

### Cache Management

The package automatically caches permissions and roles. Clear cache manually:

```php
use AmdadulHaq\Guard\Facades\Guard;

Guard::clearCache();
```

Cache is automatically invalidated when roles or permissions are created, updated, or deleted.

### Custom Exceptions

```php
use AmdadulHaq\Guard\Exceptions\PermissionDeniedException;
use AmdadulHaq\Guard\Exceptions\RoleDoesNotExistException;

if (!$user->hasPermission('users.delete')) {
    throw PermissionDeniedException::create('users.delete');
}

$role = Role::where('name', 'non-existent')->first();
if (!$role) {
    throw RoleDoesNotExistException::named('admin');
}
```

### Permission Type Enum

```php
use AmdadulHaq\Guard\Enums\PermissionType;

PermissionType::CREATE->label(); // "Create"
PermissionType::DELETE->label(); // "Delete"
PermissionType::VIEW_ANY->label(); // "View any"
PermissionType::UPDATE->label(); // "Update"
PermissionType::RESTORE->label(); // "Restore"
PermissionType::FORCE_DELETE->label(); // "Force delete"
```

### Seeding Roles and Permissions

Create seeders to set up default roles and permissions:

```php
<?php

namespace Database\Seeders;

use AmdadulHaq\Guard\Models\Permission;
use AmdadulHaq\Guard\Models\Role;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $admin = Role::create(['name' => 'admin', 'label' => 'Administrator', 'is_guarded' => true]);
        $editor = Role::create(['name' => 'editor', 'label' => 'Editor']);
        $user = Role::create(['name' => 'user', 'label' => 'User']);

        // Create permissions
        $permissions = [
            ['name' => 'users.view_any', 'label' => 'View All Users', 'group' => 'users'],
            ['name' => 'users.create', 'label' => 'Create User', 'group' => 'users'],
            ['name' => 'users.edit', 'label' => 'Edit User', 'group' => 'users'],
            ['name' => 'users.delete', 'label' => 'Delete User', 'group' => 'users'],
            ['name' => 'posts.view_any', 'label' => 'View All Posts', 'group' => 'posts'],
            ['name' => 'posts.create', 'label' => 'Create Post', 'group' => 'posts'],
            ['name' => 'posts.edit', 'label' => 'Edit Post', 'group' => 'posts'],
            ['name' => 'posts.delete', 'label' => 'Delete Post', 'group' => 'posts'],
            ['name' => 'posts.*', 'label' => 'Manage All Posts', 'group' => 'posts', 'is_wildcard' => true],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Assign permissions to roles
        $admin->syncPermissions(Permission::all()->pluck('id')->toArray());
        $editor->syncPermissions(['posts.view_any', 'posts.create', 'posts.edit', 'posts.*']);
        $user->syncPermissions(['posts.view_any']);
    }
}
```

Run the seeder:

```bash
php artisan db:seed --class=RoleAndPermissionSeeder
```

### API Protection

Protect API routes with role and permission middleware:

```php
// routes/api.php
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes - require authentication
Route::middleware('auth:sanctum')->group(function () {
    // User routes
    Route::middleware('permission:users.view_any')->get('/users', [UserController::class, 'index']);
    Route::middleware('permission:users.create')->post('/users', [UserController::class, 'store']);
    Route::middleware('permission:users.edit')->put('/users/{id}', [UserController::class, 'update']);
    Route::middleware('permission:users.delete')->delete('/users/{id}', [UserController::class, 'destroy']);

    // Post routes
    Route::middleware('permission:posts.view_any')->get('/posts', [PostController::class, 'index']);
    Route::middleware('permission:posts.create')->post('/posts', [PostController::class, 'store']);
    Route::middleware('permission:posts.edit')->put('/posts/{id}', [PostController::class, 'update']);
    Route::middleware('permission:posts.delete')->delete('/posts/{id}', [PostController::class, 'destroy']);

    // Admin only routes
    Route::middleware('role:admin')->prefix('/admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']);
        Route::get('/settings', [AdminController::class, 'settings']);
        Route::post('/settings', [AdminController::class, 'updateSettings']);
    });

    // Multiple permissions required
    Route::middleware(['permission:posts.edit', 'permission:posts.publish'])
        ->post('/posts/{id}/publish', [PostController::class, 'publish']);
});

// Using role_or_permission middleware
Route::middleware(['auth:sanctum', 'role_or_permission:admin,posts.manage'])
    ->delete('/posts/{id}', [PostController::class, 'destroy']);
```

Example API Controller with authorization:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Requests\PostRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('posts.view_any');

        $posts = Post::with('author')->latest()->paginate(20);
        return response()->json($posts);
    }

    public function store(PostRequest $request): JsonResponse
    {
        $this->authorize('posts.create');

        $post = auth()->user()->posts()->create($request->validated());
        return response()->json($post, 201);
    }

    public function update(PostRequest $request, Post $post): JsonResponse
    {
        $this->authorize('posts.edit', $post);

        $post->update($request->validated());
        return response()->json($post);
    }

    public function destroy(Post $post): JsonResponse
    {
        $this->authorize('posts.delete', $post);

        $post->delete();
        return response()->json(null, 204);
    }
}
```

## Database Structure

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
    $table->string('group')->nullable();
    $table->boolean('is_wildcard')->default(false);
    $table->timestamps();
});
```

### Permission-Role Pivot Table

```php
Schema::create('permission_role', function (Blueprint $table) {
    $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
    $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
    $table->primary(['permission_id', 'role_id']);
});
```

### Role-User Pivot Table

```php
Schema::create('role_user', function (Blueprint $table) {
    $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
    $table->primary(['role_id', 'user_id']);
});
```

## Available Enums

### CacheKey

```php
enum CacheKey: string
{
    case PERMISSIONS = 'guard_permissions';
    case ROLES = 'guard_roles';
}
```

### PermissionType

```php
enum PermissionType: string
{
    case READ = 'read';
    case WRITE = 'write';
    case DELETE = 'delete';
    case MANAGE = 'manage';
    case VIEW_ANY = 'view_any';
    case VIEW = 'view';
    case CREATE = 'create';
    case UPDATE = 'update';
    case RESTORE = 'restore';
    case FORCE_DELETE = 'force_delete';

    public function label(): string
    {
        return str_replace('_', ' ', ucfirst($this->value));
    }
}
```

## Development Tools

This package includes comprehensive developer tools:

### Code Refactoring

```bash
# Run Rector
composer refactor

# Check Rector dry-run
composer refactor:check
```

### Code Quality

```bash
# Format code with Laravel Pint
composer lint

# Check code style
composer lint:check
```

### Testing

```bash
# Run Pest tests
composer test

# Run tests with coverage
composer test-coverage
```

### Static Analysis

```bash
# Run Larastan
composer analyse
```

## Testing

Run the test suite:

```bash
composer test
```

For code coverage:

```bash
composer test-coverage
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

![Contributors](https://contrib.rocks/image?repo=amdad121/guard-laravel)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
