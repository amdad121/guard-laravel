<?php

declare(strict_types=1);

use AmdadulHaq\Guard\Models\Permission;
use AmdadulHaq\Guard\Models\Role;
use AmdadulHaq\Guard\Tests\Models\User;

it('creates a role via the artisan command', function (): void {
    $this->artisan('guard:create-role', [
        'name' => 'moderator',
        'label' => 'Moderator',
    ])
        ->expectsQuestion('ID, email, or name of the user', '')
        ->assertSuccessful();

    expect(Role::query()->where('name', 'moderator')->first())
        ->not->toBeNull()
        ->label->toBe('Moderator');
});

it('creates a role and assigns it to a user by name via the artisan command', function (): void {
    $user = User::query()->create([
        'name' => 'Command User',
        'email' => 'command-user@example.com',
        'password' => 'password',
    ]);

    $this->artisan('guard:create-role', [
        'name' => 'editor',
        'label' => 'Editor',
        'user' => $user->name,
    ])->assertSuccessful();

    expect($user->fresh()->hasRole('editor'))->toBeTrue();
});

it('fails to assign a role when the user does not exist', function (): void {
    $this->artisan('guard:create-role', [
        'name' => 'editor',
        'label' => 'Editor',
        'user' => '999',
    ])
        ->expectsOutput('User does not exist. Use a valid user ID, email, or name.')
        ->assertFailed();
});

it('creates a permission via the artisan command', function (): void {
    $this->artisan('guard:create-permission', [
        'name' => 'posts.publish',
        'label' => 'Publish Posts',
    ])
        ->expectsQuestion('ID or name of the role', '')
        ->assertSuccessful();

    expect(Permission::query()->where('name', 'posts.publish')->first())
        ->not->toBeNull()
        ->label->toBe('Publish Posts');
});

it('creates a permission and assigns it to a role by name via the artisan command', function (): void {
    $role = Role::query()->create(['name' => 'admin']);

    $this->artisan('guard:create-permission', [
        'name' => 'users.ban',
        'label' => 'Ban Users',
        'role' => $role->name,
    ])->assertSuccessful();

    expect($role->fresh()->hasPermission('users.ban'))->toBeTrue();
});

it('fails to assign a permission when the role does not exist', function (): void {
    $this->artisan('guard:create-permission', [
        'name' => 'users.ban',
        'label' => 'Ban Users',
        'role' => '999',
    ])
        ->expectsOutput('Role does not exist. Use a valid role ID or name.')
        ->assertFailed();
});
