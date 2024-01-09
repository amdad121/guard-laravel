<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Models;

use AmdadulHaq\UniqueSlug\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, HasSlug, SoftDeletes;

    protected $fillable = [
        'name', 'slug',
    ];

    public function getSlugSourceAttribute(): string
    {
        return 'name';
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function givePermissionTo(Permission $permission): Model
    {
        return $this->permissions()->save($permission);
    }
}
