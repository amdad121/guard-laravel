<?php

declare(strict_types=1);

use AmdadulHaq\Guard\Models\Permission;
use AmdadulHaq\Guard\Models\Role;

return [

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | Fully qualified class names for the models used by Guard. You can
    | extend or replace the default Role and Permission models here.
    |
    */

    'models' => [
        'user' => 'App\\Models\\User',
        'role' => Role::class,
        'permission' => Permission::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Tables
    |--------------------------------------------------------------------------
    |
    | Database table names for the roles and permissions system.
    |
    */

    'tables' => [
        'roles' => 'roles',
        'permissions' => 'permissions',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | Cache is cleared automatically when roles or permissions are updated.
    | Durations are in seconds.
    |
    */

    'cache' => [
        'enabled' => env('GUARD_CACHE_ENABLED', true),
        'roles_duration' => (int) env('GUARD_ROLES_CACHE_DURATION', 3600),
        'permissions_duration' => (int) env('GUARD_PERMISSIONS_CACHE_DURATION', 3600),
    ],

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | Middleware aliases registered by Guard for use in route definitions.
    |
    */

    'middleware' => [
        'role' => 'role',
        'permission' => 'permission',
        'role_or_permission' => 'role_or_permission',
    ],

    /*
    |--------------------------------------------------------------------------
    | Wildcard Permissions
    |--------------------------------------------------------------------------
    |
    | When enabled, a wildcard permission such as 'user.*' will match any
    | permission with the 'user.' prefix (e.g. 'user.update', 'user.delete').
    |
    */

    'wildcard' => [
        'enabled' => env('GUARD_WILDCARD_ENABLED', true),
    ],
];
