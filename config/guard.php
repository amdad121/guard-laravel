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
];
