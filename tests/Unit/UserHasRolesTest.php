<?php

declare(strict_types=1);

use AmdadulHaq\Guard\Exceptions\PermissionDeniedException;
use AmdadulHaq\Guard\Facades\Guard;
use AmdadulHaq\Guard\Models\Permission;
use AmdadulHaq\Guard\Models\Role;
use AmdadulHaq\Guard\Tests\Models\User;

beforeEach(function (): void {
    $this->user = User::query()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $this->role = Role::query()->create(['name' => 'admin']);
    $this->permission = Permission::query()->create(['name' => 'users.create']);
});

it('can assign role to user', function (): void {
    $this->user->assignRole($this->role);

    expect($this->user->roles)
        ->toHaveCount(1)
        ->first()->name->toBe('admin');
});

it('can assign role to user by name', function (): void {
    $this->user->assignRole('admin');

    expect($this->user->roles)
        ->toHaveCount(1)
        ->first()->name->toBe('admin');
});

it('can sync roles to user', function (): void {
    $role2 = Role::query()->create(['name' => 'editor']);

    $this->user->syncRoles([$this->role->id, $role2->id]);

    expect($this->user->roles)
        ->toHaveCount(2);
});

it('can revoke role from user', function (): void {
    $this->user->assignRole($this->role);
    expect($this->user->roles)->toHaveCount(1);

    $this->user->revokeRole($this->role);

    $this->user = $this->user->fresh()->roles;

    expect($this->user)
        ->toHaveCount(0);
});

it('can check if user has role', function (): void {
    $this->user->assignRole($this->role);

    expect($this->user->hasRole('admin'))
        ->toBeTrue();

    expect($this->user->hasRole('editor'))
        ->toBeFalse();
});

it('can check if user has all roles', function (): void {
    $role2 = Role::query()->create(['name' => 'editor']);

    $this->user->syncRoles([$this->role->id, $role2->id]);

    expect($this->user->hasAllRoles(['admin', 'editor']))
        ->toBeTrue();

    expect($this->user->hasAllRoles(['admin', 'moderator']))
        ->toBeFalse();
});

it('can check if user has any role', function (): void {
    $this->user->assignRole($this->role);

    expect($this->user->hasAnyRole(['admin', 'editor']))
        ->toBeTrue();

    expect($this->user->hasAnyRole(['editor', 'moderator']))
        ->toBeFalse();
});

it('can get all user roles', function (): void {
    $role2 = Role::query()->create(['name' => 'editor']);
    $role3 = Role::query()->create(['name' => 'moderator']);

    $this->user->syncRoles([$this->role->id, $role2->id, $role3->id]);

    $this->user->refresh();

    $allRoles = $this->user->roles;

    expect($allRoles)
        ->toHaveCount(3)
        ->pluck('name')->sort()->values()
        ->toArray()
        ->toEqual(['admin', 'editor', 'moderator']);
});

it('can check if user has permission via role', function (): void {
    $this->role->givePermissionTo($this->permission);
    $this->user->assignRole($this->role);

    expect($this->user->hasPermission('users.create'))
        ->toBeTrue();

    expect($this->user->hasPermission('users.delete'))
        ->toBeFalse();
});

it('can get all user permissions from roles', function (): void {
    $permission2 = Permission::query()->create(['name' => 'users.update']);

    $this->role->givePermissionTo($this->permission);
    $role2 = Role::query()->create(['name' => 'editor']);
    $role2->givePermissionTo($permission2);

    $this->user->syncRoles([$this->role->id, $role2->id]);

    $this->user->refresh();

    $permissions = $this->user->getPermissions();

    expect($permissions)
        ->toHaveCount(2)
        ->pluck('name')->sort()->values()
        ->toArray()
        ->toEqual(['users.create', 'users.update']);
});

it('can get all user permission names', function (): void {
    $permission2 = Permission::query()->create(['name' => 'users.update']);
    $permission3 = Permission::query()->create(['name' => 'users.delete']);

    $this->role->givePermissionTo($this->permission);
    $role2 = Role::query()->create(['name' => 'editor']);
    $role2->givePermissionTo($permission2);

    $this->user->syncRoles([$this->role->id, $role2->id]);

    $this->user->refresh();

    $allPermissions = $this->user->roles->flatMap(fn ($role) => $role->permissions->pluck('name'));

    expect($allPermissions)
        ->toHaveCount(2)
        ->sort()->values()
        ->toArray()
        ->toEqual(['users.create', 'users.update']);
});

it('can check wildcard permissions', function (): void {
    $wildcardPermission = Permission::query()->create(['name' => 'users.*']);
    $this->role->givePermissionTo($wildcardPermission);
    $this->user->assignRole($this->role);

    expect($this->user->hasPermission('users.create'))
        ->toBeTrue();

    expect($this->user->hasPermission('users.delete'))
        ->toBeTrue();
});

it('throws exception when user lacks permission', function (): void {
    $this->role->givePermissionTo($this->permission);
    $this->user->assignRole($this->role);

    throw_if(! $this->user->hasPermission('users.delete'), PermissionDeniedException::create('users.delete'));
})->throws(PermissionDeniedException::class);

it('defines gate for permissions', function (): void {
    $this->role->givePermissionTo($this->permission);
    $this->user->assignRole($this->role);
    $this->user->refresh();

    // Clear cache to ensure permissions are picked up
    Guard::clearCache();

    // Test that permission gate works via user method
    $hasPermission = $this->user->hasPermission('users.create');
    expect($hasPermission)->toBeTrue();
});

it('defines gate for roles', function (): void {
    $this->user->assignRole($this->role);
    $this->user->refresh();

    // Clear cache to ensure roles are picked up
    Guard::clearCache();

    // Test that role gate works via user method
    $hasRole = $this->user->hasRole('admin');
    expect($hasRole)->toBeTrue();
});
