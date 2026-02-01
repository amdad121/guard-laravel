<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Guard Package Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for the Guard package,
    | which provides role and permission management for Laravel applications.
    |
    | All configuration values have sensible defaults, but you can override
    | them in your .env file for environment-specific settings.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Guard Models
    |--------------------------------------------------------------------------
    |
    | Define the fully qualified class names for the models used by the
    | Guard package. You can extend or override the default models.
    |
    */

    'models' => [
        'user' => \App\Models\User::class,
        'role' => \AmdadulHaq\Guard\Models\Role::class,
        'permission' => \AmdadulHaq\Guard\Models\Permission::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Guard Tables
    |--------------------------------------------------------------------------
    |
    | Configure the database table names for the roles and permissions
    | system. You can customize these if you prefer different table names.
    |
    */

    'tables' => [
        'roles' => 'roles',
        'permissions' => 'permissions',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Enable caching to improve performance when checking permissions and roles.
    | Cache is cleared automatically when roles or permissions are updated.
    |
    */

    'cache' => [
        'enabled' => env('GUARD_CACHE_ENABLED', true),
        'roles_duration' => (int) env('GUARD_ROLES_CACHE_DURATION', 3600),
        'permissions_duration' => (int) env('GUARD_PERMISSIONS_CACHE_DURATION', 3600),
    ],

    /*
    |--------------------------------------------------------------------------
    | Middleware Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the middleware aliases that will be registered by the package.
    | These aliases can be used in your route definitions.
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
    | Enable wildcard permissions that allow checking permissions by prefix.
    | For example, if you have 'user.update' and 'user.delete', a wildcard
    | permission of 'user.*' will match both.
    |
    | Format: 'resource.action' where 'action' can be the asterisk wildcard '*'.
    |
    */

    'wildcard' => [
        'enabled' => env('GUARD_WILDCARD_ENABLED', true),
    ],
];
