<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Models;

use AmdadulHaq\Guard\Contracts\Permission as PermissionContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model implements PermissionContract
{
    use HasFactory;

    protected $fillable = [
        'name', 'label',
    ];

    protected $table = 'permissions';

    public function __construct()
    {
        $this->table = config('guard.tables.permissions');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(config('guard.models.role'));
    }
}
