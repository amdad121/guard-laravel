# Changelog

All notable changes to `guard-laravel` will be documented in this file.

## v1.2.1 - 2026-02-03

### ✨ New Features

- **Custom Blade Directives**:
    - `@role('role-name')` - Check if user has a specific role
    - `@hasrole('role-name')` - Alternative syntax for role checking
    - `@hasanyrole(['admin', 'editor'])` - Check if user has any of the specified roles
    - `@hasallroles(['admin', 'editor'])` - Check if user has all specified roles
    - All directives support array or string parameters
    - Automatic integration with authenticated user

### 📚 Documentation

- Updated README.md with comprehensive Blade directives documentation
- Added examples for all custom directives
- Clarified difference between custom directives and Laravel's built-in `@can` directives

### 🛠️ Code Quality

- Added `registerBladeDirectives()` method to ServiceProvider
- Properly imported `Illuminate\Support\Facades\Blade`
- All directives follow Laravel Blade conventions

---

## v1.3.0 - 2026-02-02

### 🔨 Breaking Changes

- **Contracts renamed**: `HasRoles` → `Roles`, `HasPermissions` → `Permissions`
- **Traits moved to Concerns directory**: All traits now in `src/Concerns/`
- **Middleware updates**: All middlewares now support multiple roles/permissions via variadic parameters
- **Method signatures updated**: All model assignment methods now accept `Model|string` parameters
- **Shared concerns added**: `ChecksRoles` and `ResolvesModels` traits for DRY principle

### ✨ New Features

- **Improved Architecture**:
    - Contracts and traits properly separated with no naming conflicts
    - New `ChecksRoles` trait with shared role checking logic
    - New `ResolvesModels` trait with shared model resolution logic
    - Better type hints throughout codebase
    - Improved code organization and maintainability

- **Middleware Enhancements**:
    - `RoleMiddleware`: Now supports multiple roles (comma-separated or variadic)
    - `PermissionMiddleware`: Now supports multiple permissions
    - `RoleOrPermissionMiddleware`: Now supports multiple roles or permissions
    - All middlewares handle both formats: `role:admin,editor` or `role:admin`, `role:editor`

- **Code Quality**:
    - 0 static analysis errors (Larastan)
    - All files pass Laravel Pint linting
    - Follows SOLID principles
    - Proper separation of concerns

- **Testing**:
    - Added comprehensive middleware tests (10 new tests)
    - Test User model now extends Authenticatable (proper Laravel usage)
    - All 42 tests passing (77 assertions)
    - 35 source files

- **Documentation**:
    - Updated README with new architecture
    - Complete UPGRADE.md guide
    - Clarified middleware usage with examples

### 📚 Documentation

- Updated README.md with new contract and trait structure
- Comprehensive UPGRADE.md with migration guide
- Added examples for middleware multiple parameters
- Clarified import statements for new structure

### 🛠️ Code Quality

- Refactored code to follow DRY principle
- Improved type hints for better IDE support
- Better documentation comments
- Enhanced error messages

### 🔄 Migration Requirements

Users upgrading to v1.3.0 need to:

1. Update imports from `Guard\HasRoles` to `Guard\Concerns\HasRoles`
2. Update imports from `Guard\HasPermissions` to `Guard\Concerns\HasPermissions`
3. Update middleware usage to support multiple roles/permissions (if needed)
4. Test application thoroughly after upgrade

### 📊 Statistics

- **Source files**: 35 (increased from 26)
- **Tests passing**: 42 (increased from 36)
- **Assertions**: 77 (increased from 69)
- **Static analysis**: 0 errors
- **Code quality**: All files pass Laravel Pint

### 🐛 Bug Fixes

- Fixed middleware exception handling in test environment
- Improved type resolution in model assignment methods
- Fixed import paths in test files

### 🎯 Improvements

- **SOLID Principles**: Better separation of concerns
- **DRY Principle**: Shared traits reduce code duplication
- **Naming Conventions**: Traits in Concerns, contracts as interfaces
- **Flexibility**: Middleware now supports multiple parameters
- **Maintainability**: Better code organization

## v1.1.0 - 2026-01-30

### 🔨 Breaking Changes

- Removed deprecated `detach()` usage in `syncRoles()` method
- Changed return type of `assignRole()` from `Model` to `self`
- Changed `revokeRole()` return type from `int` to `int` (number of roles remaining)

### ✨ New Features

- **HasPermissions Trait**:
    - Added `revokeAllPermissions()` method to revoke all permissions
    - Added `hasPermissionTo()` method to check if role has a permission

- **HasRoles Trait**:
    - Added `syncRolesWithoutDetaching()` method to sync roles without detaching
    - Added `revokeRole()` method to revoke a single role
    - Added `revokeRoles()` method to revoke all roles
    - Added `getRoleNames()` method to get all role names
    - Improved `hasRole()` to handle Collection types
    - Improved `hasAllRoles()` and `hasAnyRole()` to handle Collection types
    - Simplified wildcard matching using `contains()` with closure

- **Models**:
    - Removed outdated `static $table` pattern from `Permission` and `Role` models
    - Now uses Laravel's built-in `getTable()` with config fallback

- **Service Provider**:
    - Fixed `permissionsTableExists()` to use config table name
    - Improved `registerModelObservers()` readability using `->each()`
    - Improved `defineGatePermissions()` and `defineGateRoles()` readability

### 📚 Documentation

- Added comprehensive Laravel-style documentation comments to config file
- Added docblocks to all methods in Service Provider
- Updated configuration documentation with type casting examples
- Updated Contributing guide with correct script names
- Updated code examples for new methods

### 🛠️ Code Quality

- Improved type hints throughout codebase
- Added proper exception handling documentation
- Refactored code for better readability and maintainability

## v1.0.0 - 2025-01-01

### 🎉 Initial Stable Release

This marks the first stable release of Guard Laravel with full feature set and production-ready implementation.

### Features

- Complete Role and Permission management system
- PHP 8.2, 8.3, 8.4, and 8.5 support
- Laravel 10, 11, and 12 support
- Wildcard permissions with flexible matching
- Permission groups for organization
- Direct user permissions support
- Guarded roles protection
- Custom middleware for route protection
- Intelligent caching with automatic invalidation
- Query scopes for filtering users
- Custom exceptions for better error handling
- Type-safe enums for CacheKey and PermissionType
- Full Gate integration
- Blade directives for conditional rendering
- API protection examples
- Comprehensive testing with Pest
- Code quality tools (Pint, Larastan, Rector)

### Documentation

- Complete README with all features documented
- Upgrade guide for version migrations
- Detailed CHANGELOG
- API protection examples
- Database structure documentation

### Stability

- 100% test coverage
- Production-ready codebase
- Comprehensive error handling
- Backward compatibility maintained

**Full Changelog**: https://github.com/amdad121/guard-laravel/releases/tag/v1.0.0

## v0.5.0 - 2025-03-07

### Added

- Laravel 12 support
- PHP 8.5 support
- Enhanced cache management

### Updated

- Updated Illuminate dependencies to ^10.0|^11.0|^12.0
- Updated PHPStan to ^2.0|^3.0
- Updated Orchestra Testbench to ^8.0|^9.0|^10.0
- Updated Pest to ^2.0|^3.0|^4.0

**Full Changelog**: https://github.com/amdad121/guard-laravel/compare/v0.4.0...v0.5.0

## v0.4.0 - 2025-03-07

### Added

- PHP 8.4 support
- Improved wildcard permission matching
- Added `hasAllPermissions()` method for checking multiple permissions
- Added `hasAnyPermission()` method for checking at least one permission
- Added `getPermissionsByRole()` method to get permissions grouped by role

### Updated

- Enhanced cache invalidation logic
- Improved middleware error handling
- Updated documentation with API protection examples

### Fixed

- Fixed cache invalidation when permissions are revoked
- Fixed query scopes for complex permission checks

**Full Changelog**: https://github.com/amdad121/guard-laravel/compare/v0.3.0...v0.4.0

## v0.3.0 - 2024-10-08

### Added

- Permission Groups - organize permissions into logical groups
- Direct user permissions - assign permissions directly to users without roles
- `hasDirectPermission()` method to check direct user permissions
- `getDirectPermissions()` method to get direct user permissions
- `getAllPermissions()` method to get all permissions (roles + direct)
- Enhanced middleware with `role_or_permission` middleware

### Updated

- Improved wildcard permission handling
- Enhanced query scopes for better filtering
- Updated configuration options

### Fixed

- Fixed permission sync when using IDs vs names
- Fixed cache issues with direct permissions

**Full Changelog**: https://github.com/amdad121/guard-laravel/compare/v0.2.2...v0.3.0

## v0.2.2 - 2024-10-08

### Fixed

- Fixed role assignment when using role names
- Fixed permission checking with wildcard patterns
- Fixed cache invalidation on role/permission updates

**Full Changelog**: https://github.com/amdad121/guard-laravel/compare/v0.2.1...v0.2.2

## v0.2.1 - 2024-10-08

### Added

- Added `isProtectedRole()` helper method to Role model
- Added `getPermissionNames()` helper method to Role model
- Added query scopes for guarded and unguarded roles

### Fixed

- Fixed guarded role protection logic
- Fixed permission sync method to handle both IDs and names

**Full Changelog**: https://github.com/amdad121/guard-laravel/compare/v0.2.0...v0.2.1

## v0.2.0 - 2024-10-08

### Added

- Guarded Roles - protect certain roles from deletion/modification
- Custom Exceptions for better error handling
- Permission Type Enum for type-safe constants
- Cache Key Enum for cache management
- `isWildcard()` helper method to Permission model
- `getGroup()` helper method to Permission model
- Enhanced permission checking with wildcard support

### Updated

- Improved cache management with automatic invalidation
- Enhanced error messages and exceptions
- Updated migration stubs for better database structure

**Full Changelog**: https://github.com/amdad121/guard-laravel/compare/v0.1.1...v0.2.0

## v0.1.1 - 2024-10-08

### Added

- Query scopes for filtering users by roles and permissions
- `withRoles()` scope to filter users by role
- `withPermissions()` scope to filter users by permission

### Fixed

- Fixed role and permission relationship loading
- Fixed cache clearing functionality

**Full Changelog**: https://github.com/amdad121/guard-laravel/compare/v0.1.0...v0.1.1

## v0.1.0 - 2024-10-08

### Initial Release

- Basic Role and Permission management system
- Role assignment to users
- Permission assignment to roles
- Wildcard permission support
- Custom middleware for route protection
- Gate integration
- Blade directives for conditional rendering
- Cache support for improved performance
- Query scopes for filtering users
- Database migrations for roles, permissions, and pivot tables
- Configuration options for customization

**Full Changelog**: https://github.com/amdad121/guard-laravel/commits/v0.1.0
