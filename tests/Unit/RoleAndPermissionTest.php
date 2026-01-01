<?php

declare(strict_types=1);

use AmdadulHaq\Guard\Enums\PermissionType;
use AmdadulHaq\Guard\Models\Permission;
use AmdadulHaq\Guard\Models\Role;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function (): void {
    $this->role = Role::create([
        'name' => 'admin',
        'label' => 'Administrator',
    ]);

    $this->permission = Permission::create([
        'name' => 'users.create',
        'label' => 'Create Users',
    ]);
});

it('can create a role', function (): void {
    expect($this->role)
        ->name->toBe('admin')
        ->label->toBe('Administrator');

    assertDatabaseHas('roles', [
        'name' => 'admin',
    ]);
});

it('can create a permission', function (): void {
    expect($this->permission)
        ->name->toBe('users.create')
        ->label->toBe('Create Users');

    assertDatabaseHas('permissions', [
        'name' => 'users.create',
    ]);
});

it('can assign permission to role', function (): void {
    $this->role->givePermissionTo($this->permission);

    expect($this->role->permissions)
        ->toHaveCount(1)
        ->first()->name->toBe('users.create');
});

it('can sync permissions to role', function (): void {
    $permission2 = Permission::create([
        'name' => 'users.update',
        'label' => 'Update Users',
    ]);

    $this->role->syncPermissions([$this->permission->id, $permission2->id]);

    expect($this->role->permissions)
        ->toHaveCount(2);

    $this->role->syncPermissions([]);

    expect($this->role->fresh()->permissions)
        ->toHaveCount(0);
});

it('can revoke permission from role', function (): void {
    $this->role->givePermissionTo($this->permission);
    expect($this->role->permissions)->toHaveCount(1);

    $this->role->permissions()->detach($this->permission->id);

    expect($this->role->fresh()->permissions)
        ->toHaveCount(0);
});

it('can check if permission is wildcard', function (): void {
    $wildcardPermission = Permission::create([
        'name' => 'posts.*',
        'label' => 'All Posts Permissions',
    ]);

    expect($wildcardPermission->isWildcard())
        ->toBeTrue();

    expect($this->permission->isWildcard())
        ->toBeFalse();
});

it('can get permission group', function (): void {
    expect($this->permission->getGroup())
        ->toBe('users');
});

it('can get permission type', function (): void {
    $permission = Permission::create([
        'name' => 'posts.create',
        'label' => 'Create Posts',
    ]);

    expect($permission->getType())
        ->toBe(PermissionType::CREATE);
});

it('throws exception when role does not exist', function (): void {
    Role::where('name', 'non-existent')->firstOrFail();
})->throws(ModelNotFoundException::class);

it('throws exception when permission does not exist', function (): void {
    Permission::where('name', 'non-existent')->firstOrFail();
})->throws(ModelNotFoundException::class);
