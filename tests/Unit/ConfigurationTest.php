<?php

declare(strict_types=1);

use AmdadulHaq\Guard\GuardServiceProvider;
use AmdadulHaq\Guard\Tests\Models\CustomPermission;
use AmdadulHaq\Guard\Tests\Models\CustomRole;
use AmdadulHaq\Guard\Tests\Models\User;
use Illuminate\Database\Eloquent\Collection;

it('uses configured model classes when resolving cached roles and permissions', function (): void {
    config()->set('guard.models.role', CustomRole::class);
    config()->set('guard.models.permission', CustomPermission::class);
    config()->set('guard.tables.roles', 'roles');
    config()->set('guard.tables.permissions', 'permissions');
    config()->set('guard.cache.enabled', false);

    CustomRole::query()->create(['name' => 'admin']);
    CustomPermission::query()->create(['name' => 'users.create']);

    $provider = new class($this->app) extends GuardServiceProvider
    {
        public function roles(): Collection
        {
            return $this->getRoles();
        }

        public function permissions(): Collection
        {
            return $this->getPermissions();
        }
    };

    expect($provider->roles()->first())
        ->toBeInstanceOf(CustomRole::class)
        ->and($provider->permissions()->first())
        ->toBeInstanceOf(CustomPermission::class);
});

it('uses the configured pivot table for the role users relation', function (): void {
    config()->set('guard.models.role', CustomRole::class);
    config()->set('guard.tables.roles', 'team_roles');

    $role = new CustomRole;

    expect($role->users()->getTable())->toBe('team_role_user');
});

it('supports runtime role and permission checks with configured models', function (): void {
    config()->set('guard.models.role', CustomRole::class);
    config()->set('guard.models.permission', CustomPermission::class);
    config()->set('guard.cache.enabled', false);

    $role = CustomRole::query()->create(['name' => 'admin']);
    $permission = CustomPermission::query()->create(['name' => 'users.create']);
    $user = User::query()->create([
        'name' => 'Configured User',
        'email' => 'configured@example.com',
        'password' => 'password',
    ]);

    $role->givePermissionTo($permission);
    $user->assignRole($role);

    expect($user->fresh()->hasRole('admin'))->toBeTrue()
        ->and($user->fresh()->hasPermission('users.create'))->toBeTrue();
});
