# Guard Laravel - Claude Code Guidelines

## Development Commands

```bash
# Format code with Laravel Pint
composer lint

# Check code style without modifying files
composer lint:check

# Run static analysis with Larastan
composer analyse

# Run code refactoring with Rector
composer refactor

# Check Rector dry-run
composer refactor:check

# Run Pest tests
composer test

# Run tests with coverage
composer test-coverage
```

## Architecture Overview

### Package Purpose

Guard is a modern Role and Permission management system for Laravel 10, 11, and 12 with PHP 8.2-8.5 support. It provides comprehensive ACL functionality with wildcard permissions, caching, and middleware protection.

### Key Components

#### Contracts (`src/Contracts/`)

Define the interface that models must implement:

- **User**: Interface for models using roles/permissions (methods: `hasRole()`, `hasPermission()`, `assignRole()`, `syncRoles()`, etc.)
- **Role**: Interface for role models (methods: `getName()`, `permissions()`, `givePermissionTo()`, `syncPermissions()`, etc.)
- **Permission**: Interface for permission models (methods: `getName()`, `roles()`, `isWildcard()`, `getGroup()`, `getType()`, etc.)

#### Models (`src/Models/`)

- **Role**: Eloquent model with `is_guarded` boolean for protecting system-critical roles
- **Permission**: Eloquent model with wildcard detection and grouping support

#### Traits (`src/`)

- **HasRoles**: Primary trait for user models, implements all role management methods
- **HasPermissions**: Trait for role models, implements all permission management methods

#### Service Provider (`src/GuardServiceProvider.php`)

- Registers models, commands, middleware
- Defines Gates for authorization (`Gate::define()`)
- Handles automatic cache invalidation via model observers
- Validates configured models exist (skips user model during tests)

#### Middleware

- **RoleMiddleware**: Checks user has specific role
- **PermissionMiddleware**: Checks user has specific permission
- **RoleOrPermissionMiddleware**: Checks user has role OR permission

#### Commands

- `guard:create-role`: Create new roles with optional label and user assignment
- `guard:create-permission`: Create new permissions with optional label and role assignment

### Laravel Integration

#### Service Provider Registration

Automatically registered via `extra.laravel.providers` in composer.json

#### Facade

```php
use AmdadulHaq\Guard\Facades\Guard;
```

#### Configuration

Published to `config/guard.php` with:

- `models`: User, Role, Permission model mappings
- `tables`: Custom table names
- `cache`: Cache duration settings
- `middleware`: Middleware aliases
- `wildcard`: Enable wildcard permissions

### Role-Permission Relationship

#### Database Structure

- **roles**: `id`, `name`, `label`, `description`, `is_guarded`, timestamps
- **permissions**: `id`, `name`, `label`, `description`, `group`, `is_wildcard`, timestamps
- **permission_role**: Pivot table for role-permission relationships
- **role_user**: Pivot table for user-role relationships

#### Key Features

- Many-to-many relationships between roles and permissions
- Many-to-many between users and roles
- Wildcard permissions (`posts.*` matches `posts.create`, `posts.edit`, etc.)
- Permission groups for organization
- Guarded roles that can't be deleted

### Caching Mechanism

#### Automatic Cache Invalidation

- Cache keys defined in `CacheKey` enum (`guard_roles`, `guard_permissions`)
- Cache cleared automatically on model save/deleted via observers

#### Manual Cache Clear

```php
Guard::clearCache();
```

### Wildcard Permissions System

#### Implementation

- Permissions ending with `*` are automatically marked as wildcards in the `booted()` static method
- Wildcard matching uses pattern prefix comparison (e.g., `posts.*` matches `posts.create`)
- Pattern format: `resource.action` where `action` can be `*`

#### Usage

```php
// Create wildcard permission
$permission = Permission::create(['name' => 'posts.*']);

// User with wildcard permission can access all matching permissions
$user->hasPermission('posts.create'); // returns true
```

### Contract Implementation Requirements

All contract interfaces must be implemented by their respective models. When implementing `UserContract`:

```php
use AmdadulHaq\Guard\Contracts\User as UserContract;
use AmdadulHaq\Guard\HasPermissions;
use AmdadulHaq\Guard\HasRoles;

class User extends Authenticatable implements UserContract
{
    use HasPermissions, HasRoles;
}
```

### Important Implementation Notes

#### Type Safety

- Uses PHP 8.2+ strict types on all files
- Enums for `CacheKey` and `PermissionType`
- Return types on all public methods
- Collection type is `Illuminate\Support\Collection` for methods returning permission/role names

#### Exception Handling

- Custom exceptions in `src/Exceptions/`
- `PermissionDeniedException` for failed middleware checks
- `RoleDoesNotExistException` for missing roles
- `PermissionDoesNotExistException` for missing permissions

#### Test Models

Test models in `tests/Models/User.php` must implement the same methods as the actual User model, including `getRoleNames()` and `getPermissionNames()`.

#### Guarded Roles

Protected roles (with `is_guarded = true`) cannot be deleted through standard operations. Use `isProtectedRole()` to check protection status.
