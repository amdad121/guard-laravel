# Guard for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/amdad121/guard-laravel.svg?style=flat-square)](https://packagist.org/packages/amdad121/guard-laravel)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/amdad121/guard-laravel/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/amdad121/guard-laravel/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/amdad121/guard-laravel/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/amdad121/guard-laravel/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/amdad121/guard-laravel.svg?style=flat-square)](https://packagist.org/packages/amdad121/guard-laravel)

Guard is Role and Permission management system for Laravel

## Installation

You can install the package via composer:

```bash
composer require amdad121/guard-laravel
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="guard-laravel-migrations"
php artisan migrate
```

## Usage

### Role Create

```php
use AmdadulHaq\Guard\Models\Role;

Role::create(['name' => 'Administrator']);
```

### Permission Create

```php
use AmdadulHaq\Guard\Models\Permission;
use AmdadulHaq\Guard\Models\Role;

$items = [
    ['name' => 'show', 'for' => 'role'],
    ['name' => 'create', 'for' => 'role'],
    ['name' => 'edit', 'for' => 'role'],
    ['name' => 'destroy', 'for' => 'role'],
    ['name' => 'restore', 'for' => 'role'],

    ['name' => 'show', 'for' => 'permission'],
    ['name' => 'create', 'for' => 'permission'],
    ['name' => 'edit', 'for' => 'permission'],
    ['name' => 'destroy', 'for' => 'permission'],
    ['name' => 'restore', 'for' => 'permission'],
];

$ids = [];
foreach ($items as $key => $item) {
    $permission = Permission::create($item);
    $ids[] = $permission->id;
}

$role = Role::first();
$role->permissions()->attach($ids);
```

### Assign Role and Permission

```php
use AmdadulHaq\Guard\Models\Permission;
use AmdadulHaq\Guard\Models\Role;
use App\Models\User;

$user = User::first();

$role = Role::first();

// Assign role
$user->assignRole($role);

$permission = Permission::first();

// Assign permission
$role->givePermissionTo($permission);
```

### Check Role and Permission

```php
use AmdadulHaq\Guard\Models\Permission;
use AmdadulHaq\Guard\Models\Role;
use App\Models\User;

$user = User::first();

$role = Role::first();

// Role check
$user->hasRole($role->slug)
// result: true or false

$permission = Permission::first();

// Permission check
$user->hasPermission($permission);
// result: true or false
```

### You can use multiple ways, some of are given bellow:

```php
// for permission
Gate::authorize('role.show');

// for role
Gate::authorize('administrator');
```

```php
// for permission
$this->authorize('role.show');

// for role
$this->authorize('administrator');
```

```php
// for permission
middleware('can:role.show');

// for role
middleware('can:administrator');
```

```blade
// for permission
@can('role.show')
    It's works
@endcan

// for role
@can('administrator')
    It's works
@endcan
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Amdadul Haq](https://github.com/amdad121)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
