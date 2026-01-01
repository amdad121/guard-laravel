<?php

declare(strict_types=1);

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
        'permissions_duration' => env('GUARD_PERMISSIONS_CACHE_DURATION', 3600),
        'roles_duration' => env('GUARD_ROLES_CACHE_DURATION', 3600),
        'enabled' => env('GUARD_CACHE_ENABLED', true),
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
