<?php

declare(strict_types=1);

use AmdadulHaq\Guard\Models\Permission;
use AmdadulHaq\Guard\Models\Role;
use AmdadulHaq\Guard\Tests\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function (): void {
    $this->role = Role::query()->create([
        'name' => 'admin',
        'label' => 'Administrator',
    ]);

    $this->permission = Permission::query()->create([
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

it('can assign permission to role', function (): void {
    $this->role->givePermissionTo($this->permission);

    expect($this->role->permissions)
        ->toHaveCount(1)
        ->first()->name->toBe('users.create');
});

it('can sync permissions to role', function (): void {
    $permission2 = Permission::query()->create([
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

    $this->role->revokePermissionTo($this->permission);

    expect($this->role->fresh()->permissions)
        ->toHaveCount(0);
});

it('throws exception when role does not exist', function (): void {
    Role::query()->where('name', 'non-existent')->firstOrFail();
})->throws(ModelNotFoundException::class);

it('can get all users with a role', function (): void {
    $user1 = User::query()->create([
        'name' => 'User 1',
        'email' => 'user1@example.com',
        'password' => 'password',
    ]);

    $user2 = User::query()->create([
        'name' => 'User 2',
        'email' => 'user2@example.com',
        'password' => 'password',
    ]);

    $user3 = User::query()->create([
        'name' => 'User 3',
        'email' => 'user3@example.com',
        'password' => 'password',
    ]);

    $this->role->users()->attach([$user1->id, $user2->id]);

    $usersWithRole = $this->role->users;

    expect($usersWithRole)
        ->toHaveCount(2)
        ->pluck('name')->sort()->values()
        ->toArray()
        ->toEqual(['User 1', 'User 2']);
});

it('can check if user belongs to role via users relation', function (): void {
    $user = User::query()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $this->role->users()->attach($user->id);

    $hasUser = $this->role->users()->where('id', $user->id)->exists();

    expect($hasUser)->toBeTrue();
});
