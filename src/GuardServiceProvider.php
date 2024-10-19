<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard;

use AmdadulHaq\Guard\Commands\CreatePermission;
use AmdadulHaq\Guard\Commands\CreateRole;
use AmdadulHaq\Guard\Contracts\Permission as PermissionContract;
use AmdadulHaq\Guard\Contracts\Role as RoleContract;
use AmdadulHaq\Guard\Contracts\User as UserContract;
use AmdadulHaq\Guard\Models\Permission;
use AmdadulHaq\Guard\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class GuardServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         */
        $package
            ->name('guard-laravel')
            ->hasConfigFile('guard')
            ->hasMigrations(['create_roles_table', 'create_permissions_table'])
            ->hasCommands([CreateRole::class, CreatePermission::class]);
    }

    public function bootingPackage(): void
    {
        parent::bootingPackage();

        if ($this->permissionsTableExists()) {
            $this->defineGatePermissions();
            $this->defineGateRoles();
        } else {
            info('guard-laravel: Database not found or not yet migrated. Ignoring user permissions while booting app.');
        }
    }

    public function registeringPackage(): void
    {
        parent::registeringPackage();

        $this->app->bind(PermissionContract::class, fn ($app) => $app->make($app->config['guard.models.permission']));
        $this->app->bind(RoleContract::class, fn ($app) => $app->make($app->config['guard.models.role']));
    }

    protected function permissionsTableExists(): bool
    {
        return Schema::hasTable('permissions');
    }

    protected function defineGatePermissions(): void
    {
        foreach ($this->getPermissions() as $permission) {
            /** @phpstan-ignore-next-line */
            Gate::define($permission->name, fn (UserContract $user) => $user->hasPermission($permission));
        }
    }

    protected function defineGateRoles(): void
    {
        foreach ($this->getRoles() as $role) {
            /** @phpstan-ignore-next-line */
            Gate::define($role->name, fn (UserContract $user) => $user->hasRole($role->name));
        }
    }

    protected function getPermissions(): Collection
    {
        $cacheDuration = config('guard.cache.permissions_duration', 3600); // Default to 3600 seconds if not set

        return Cache::remember('permissions', $cacheDuration, function () {
            return Permission::with('roles')->get();
        });
    }

    protected function getRoles(): Collection
    {
        $cacheDuration = config('guard.cache.roles_duration', 3600); // Default to 3600 seconds if not set

        return Cache::remember('roles', $cacheDuration, function () {
            return Role::all();
        });
    }

    public static function clearCache(): void
    {
        Cache::forget('permissions');
        Cache::forget('roles');
    }
}
