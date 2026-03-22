<?php

declare(strict_types=1);

use AmdadulHaq\Guard\Enums\PermissionType;
use AmdadulHaq\Guard\Models\Permission;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function (): void {
    $this->permission = Permission::query()->create([
        'name' => 'users.create',
        'label' => 'Create Users',
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

it('can check if permission is wildcard', function (): void {
    $wildcardPermission = Permission::query()->create([
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
    $permission = Permission::query()->create([
        'name' => 'posts.create',
        'label' => 'Create Posts',
    ]);

    expect($permission->getType())
        ->toBe(PermissionType::CREATE);
});

it('throws exception when permission does not exist', function (): void {
    Permission::query()->where('name', 'non-existent')->firstOrFail();
})->throws(ModelNotFoundException::class);

it('can query wildcard permissions via scope', function (): void {
    Permission::query()->create([
        'name' => 'posts.*',
        'label' => 'All Posts Permissions',
    ]);

    expect(Permission::query()->wildcard()->pluck('name')->all())
        ->toEqual(['posts.*']);
});

it('can query permissions by group via scope', function (): void {
    Permission::query()->create([
        'name' => 'users.delete',
        'label' => 'Delete Users',
    ]);

    Permission::query()->create([
        'name' => 'posts.create',
        'label' => 'Create Posts',
    ]);

    expect(Permission::query()->byGroup('users')->pluck('name')->sort()->values()->all())
        ->toEqual(['users.create', 'users.delete']);
});
