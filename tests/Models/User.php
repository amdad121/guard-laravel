<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Tests\Models;

use AmdadulHaq\Guard\Concerns\HasPermissions;
use AmdadulHaq\Guard\Concerns\HasRoles;
use AmdadulHaq\Guard\Contracts\User as UserContract;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements UserContract
{
    use HasPermissions;
    use HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }
}
