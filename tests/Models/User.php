<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Tests\Models;

use AmdadulHaq\Guard\Contracts\User as UserContract;
use AmdadulHaq\Guard\HasPermissions;
use AmdadulHaq\Guard\HasRoles;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements UserContract
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
