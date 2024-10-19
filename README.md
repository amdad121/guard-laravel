# Guard Role And Permission Package For Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/amdadulhaq/guard-laravel.svg?style=flat-square)](https://packagist.org/packages/amdadulhaq/guard-laravel)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/amdadulhaq/guard-laravel/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/amdadulhaq/guard-laravel/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/amdadulhaq/guard-laravel/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/amdadulhaq/guard-laravel/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/amdadulhaq/guard-laravel.svg?style=flat-square)](https://packagist.org/packages/amdadulhaq/guard-laravel)

Guard is Role and Permission management system for Laravel

## Installation

You can install the package via composer:

```bash
composer require amdadulhaq/guard-laravel
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="guard-laravel-migrations"
php artisan migrate
```

Add `HasRoles` Trait and `UserContract` Interface on User Model

```php
namespace App\Models;

use AmdadulHaq\Guard\Contracts\User as UserContract;
use AmdadulHaq\Guard\HasRoles;
# ...

class User extends Authenticatable implements UserContract
{
    use HasRoles;
}
```

## Usage

### Role Create

```php
use AmdadulHaq\Guard\Models\Role;

Role::create(['name' => 'administrator']);
```

### Permission Create

```php
use AmdadulHaq\Guard\Models\Permission;
use AmdadulHaq\Guard\Models\Role;

$items = [
    'role' => ['viewAny', 'view', 'create', 'update', 'delete', 'restore', 'forceDelete'],
    'permission' => ['viewAny', 'view', 'create', 'update', 'delete'],
];

$role = Role::first();

foreach ($items as $group => $names) {
    foreach ($names as $name) {
        $permission = Permission::create(['name' => $group.'.'.$name]);

        $role->givePermissionTo($permission);
    }
}
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

### Revoke Role and Permission

```php
use AmdadulHaq\Guard\Models\Permission;
use AmdadulHaq\Guard\Models\Role;
use App\Models\User;

$user = User::first();

$role = Role::first();

// Revoke role
$user->revokeRole($role);

$permission = Permission::first();

// Revoke permission
$role->revokePermissionTo($permission);
```

### Check Role and Permission

```php
use AmdadulHaq\Guard\Models\Permission;
use AmdadulHaq\Guard\Models\Role;
use App\Models\User;

$user = User::first();

$role = Role::first();

// Role check
$user->hasRole($role->name) // true or false

$permission = Permission::first();

// Permission check
$user->hasPermission($permission); // true or false
```

### You can use multiple ways, some of are given bellow:

```php
use Illuminate\Support\Facades\Gate;

// for permission
Gate::authorize('role.view');

// for role
Gate::authorize('administrator');
```

```php
// for permission
$this->authorize('role.view');

// for role
$this->authorize('administrator');
```

```php
use Illuminate\Support\Facades\Route;

// for permission
Route::get('/', function () {
    // ...
})->middleware('can:role.view');

// for role
Route::get('/', function () {
    // ...
})->middleware('can:administrator');
```

```blade
// for permission
@can('role.view')
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
