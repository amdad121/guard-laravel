<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Tests\Models;

use AmdadulHaq\Guard\Concerns\Roleable;
use AmdadulHaq\Guard\Contracts\Roleable as RoleableContract;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements RoleableContract
{
    use Roleable;

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
