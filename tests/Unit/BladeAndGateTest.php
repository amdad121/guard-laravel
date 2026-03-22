<?php

declare(strict_types=1);

use AmdadulHaq\Guard\Facades\Guard;
use AmdadulHaq\Guard\GuardServiceProvider;
use AmdadulHaq\Guard\Models\Permission;
use AmdadulHaq\Guard\Models\Role;
use AmdadulHaq\Guard\Tests\Models\User;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;

beforeEach(function (): void {
    $this->user = User::query()->create([
        'name' => 'Blade User',
        'email' => 'blade@example.com',
        'password' => 'password',
    ]);

    $this->role = Role::query()->create(['name' => 'admin']);
    $this->permission = Permission::query()->create(['name' => 'users.create']);

    $this->role->givePermissionTo($this->permission);
    $this->user->assignRole($this->role);
    $this->user->refresh();
    Guard::clearCache();

    /** @var GuardServiceProvider $provider */
    $provider = $this->app->getProvider(GuardServiceProvider::class);
    $provider->defineGatePermissions();
    $provider->defineGateRoles();
});

it('authorizes permissions through Laravel Gate', function (): void {
    expect(Gate::forUser($this->user)->allows('users.create'))->toBeTrue()
        ->and(Gate::forUser($this->user)->allows('users.delete'))->toBeFalse();
});

it('authorizes roles through Laravel Gate', function (): void {
    expect(Gate::forUser($this->user)->allows('admin'))->toBeTrue()
        ->and(Gate::forUser($this->user)->allows('editor'))->toBeFalse();
});

it('renders custom blade role directives', function (): void {
    $this->be($this->user);

    $html = Blade::render(
        <<<'BLADE'
        @role('admin')
        <span>admin</span>
        @endrole
        @hasrole('editor')
        <span>editor</span>
        @endhasrole
        BLADE,
        [],
        deleteCachedView: true
    );

    expect($html)->toContain('admin')
        ->not->toContain('editor');
});

it('renders custom blade multiple-role directives', function (): void {
    $this->be($this->user);

    $html = Blade::render(
        <<<'BLADE'
        @hasanyrole(['admin', 'editor'])
        <span>any-role</span>
        @endhasanyrole
        @hasallroles(['admin', 'editor'])
        <span>all-roles</span>
        @endhasallroles
        BLADE,
        [],
        deleteCachedView: true
    );

    expect($html)->toContain('any-role')
        ->not->toContain('all-roles');
});
