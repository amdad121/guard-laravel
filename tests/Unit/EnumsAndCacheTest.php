<?php

declare(strict_types=1);

use AmdadulHaq\Guard\Enums\CacheKey;
use AmdadulHaq\Guard\Enums\PermissionType;
use AmdadulHaq\Guard\Models\Permission;
use AmdadulHaq\Guard\Models\Role;
use Illuminate\Support\Facades\Cache;

it('has correct cache key values', function (): void {
    expect(CacheKey::PERMISSIONS->value)->toBe('guard_permissions');
    expect(CacheKey::ROLES->value)->toBe('guard_roles');
});

it('has permission type enum values', function (): void {
    expect(PermissionType::READ->value)->toBe('read');
    expect(PermissionType::CREATE->value)->toBe('create');
    expect(PermissionType::UPDATE->value)->toBe('update');
    expect(PermissionType::DELETE->value)->toBe('delete');
});

it('permission type has label method', function (): void {
    expect(PermissionType::VIEW_ANY->label())->toBe('View any');
    expect(PermissionType::FORCE_DELETE->label())->toBe('Force delete');
});

it('caches permissions', function (): void {
    Permission::query()->create(['name' => 'test.permission']);

    $cacheKey = CacheKey::PERMISSIONS->value;

    expect(Cache::has($cacheKey))->toBeFalse();

    $permissions = Permission::with('roles')->get();
    Cache::put($cacheKey, $permissions, 3600);

    expect(Cache::has($cacheKey))->toBeTrue();
    expect(Cache::get($cacheKey))->toHaveCount(1);
});

it('caches roles', function (): void {
    Role::query()->create(['name' => 'test-role']);

    $cacheKey = CacheKey::ROLES->value;

    expect(Cache::has($cacheKey))->toBeFalse();

    $roles = Role::all();
    Cache::put($cacheKey, $roles, 3600);

    expect(Cache::has($cacheKey))->toBeTrue();
    expect(Cache::get($cacheKey))->toHaveCount(1);
});

it('clears cache on role deletion', function (): void {
    $role = Role::query()->create(['name' => 'test-role']);

    $cacheKey = CacheKey::ROLES->value;
    Cache::put($cacheKey, Role::all(), 3600);

    expect(Cache::has($cacheKey))->toBeTrue();

    $role->delete();

    expect(Cache::has($cacheKey))->toBeFalse();
});

it('clears cache on permission deletion', function (): void {
    $permission = Permission::query()->create(['name' => 'test.permission']);

    $cacheKey = CacheKey::PERMISSIONS->value;
    Cache::put($cacheKey, Permission::with('roles')->get(), 3600);

    expect(Cache::has($cacheKey))->toBeTrue();

    $permission->delete();

    expect(Cache::has($cacheKey))->toBeFalse();
});

it('clears cache when permissions are assigned to roles', function (): void {
    config()->set('guard.cache.enabled', true);

    $role = Role::query()->create(['name' => 'test-role']);
    $permission = Permission::query()->create(['name' => 'test.permission']);

    Cache::put(CacheKey::PERMISSIONS->value, Permission::with('roles')->get(), 3600);
    Cache::put(CacheKey::ROLES->value, Role::all(), 3600);

    expect(Cache::has(CacheKey::PERMISSIONS->value))->toBeTrue();
    expect(Cache::has(CacheKey::ROLES->value))->toBeTrue();

    $role->givePermissionTo($permission);

    expect(Cache::has(CacheKey::PERMISSIONS->value))->toBeFalse();
    expect(Cache::has(CacheKey::ROLES->value))->toBeFalse();
});
