# Changelog

All notable changes to `guard-laravel` will be documented in this file.

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
