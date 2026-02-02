<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard;

use AmdadulHaq\Guard\Commands\CreatePermission;
use AmdadulHaq\Guard\Commands\CreateRole;
use AmdadulHaq\Guard\Contracts\Permissions as PermissionsContract;
use AmdadulHaq\Guard\Contracts\Roles as RolesContract;
use AmdadulHaq\Guard\Contracts\User as UserContract;
use AmdadulHaq\Guard\Enums\CacheKey;
use AmdadulHaq\Guard\Facades\Guard;
use AmdadulHaq\Guard\Middleware\PermissionMiddleware;
use AmdadulHaq\Guard\Middleware\RoleMiddleware;
use AmdadulHaq\Guard\Middleware\RoleOrPermissionMiddleware;
use AmdadulHaq\Guard\Models\Permission;
use AmdadulHaq\Guard\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Throwable;

class GuardServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/guard.php',
            'guard'
        );

        $this->app->bind(PermissionsContract::class, fn () => resolve(config('guard.models.permission')));
        $this->app->bind(RolesContract::class, fn () => resolve(config('guard.models.role')));
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishConfig();
        $this->publishMigrations();
        $this->registerCommands();
        $this->registerMiddleware();

        if ($this->permissionsTableExists()) {
            $this->registerModelObservers();
            $this->defineGatePermissions();
            $this->defineGateRoles();
        }
    }

    /**
     * Define gate permissions for the application.
     */
    public function defineGatePermissions(): void
    {
        $this->getPermissions()
            ->each(fn (Permission $permission) => Gate::define(
                $permission->getName(),
                fn (UserContract $user): bool => $user->hasPermissionByName($permission->getName())
            ));
    }

    /**
     * Define gate roles for the application.
     */
    public function defineGateRoles(): void
    {
        $this->getRoles()
            ->each(fn (Role $role) => Gate::define(
                $role->getName(),
                fn (UserContract $user): bool => $user->hasRole($role->getName())
            ));
    }

    /**
     * Publish the configuration file.
     */
    protected function publishConfig(): void
    {
        $this->publishes([
            __DIR__.'/../config/guard.php' => config_path('guard.php'),
        ], 'guard-config');
    }

    /**
     * Publish the migration files.
     */
    protected function publishMigrations(): void
    {
        $this->publishes([
            __DIR__.'/../database/migrations/create_roles_table.php.stub' => database_path('migrations/'.date('Y_m_d_His').'_create_roles_table.php'),
            __DIR__.'/../database/migrations/create_permissions_table.php.stub' => database_path('migrations/'.date('Y_m_d_His').'_create_permissions_table.php'),
        ], 'guard-migrations');
    }

    /**
     * Register the console commands for this package.
     */
    protected function registerCommands(): void
    {
        $this->commands([
            CreateRole::class,
            CreatePermission::class,
        ]);
    }

    /**
     * Register the package middleware.
     */
    protected function registerMiddleware(): void
    {
        $router = $this->app->make(Router::class);

        $router->aliasMiddleware('role', RoleMiddleware::class);
        $router->aliasMiddleware('permission', PermissionMiddleware::class);
        $router->aliasMiddleware('role_or_permission', RoleOrPermissionMiddleware::class);
    }

    /**
     * Register model observers for cache clearing.
     */
    protected function registerModelObservers(): void
    {
        collect([Role::class, Permission::class])
            ->each(function (string $model): void {
                $model::saved(fn () => Guard::clearCache());
                $model::deleted(fn () => Guard::clearCache());
            });
    }

    /**
     * Check if the permissions table exists.
     */
    protected function permissionsTableExists(): bool
    {
        try {
            return Schema::hasTable(config('guard.tables.permissions'));
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Get all permissions from cache or database.
     *
     * @return Collection<int, Permission>
     */
    protected function getPermissions(): Collection
    {
        if (! config('guard.cache.enabled', true)) {
            return Permission::with('roles')->get();
        }

        $cacheDuration = config('guard.cache.permissions_duration', 3600);

        return Cache::remember(CacheKey::PERMISSIONS->value, $cacheDuration, fn () => Permission::with('roles')->get());
    }

    /**
     * Get all roles from cache or database.
     *
     * @return Collection<int, Role>
     */
    protected function getRoles(): Collection
    {
        if (! config('guard.cache.enabled', true)) {
            return Role::all();
        }

        $cacheDuration = config('guard.cache.roles_duration', 3600);

        return Cache::remember(CacheKey::ROLES->value, $cacheDuration, fn () => Role::all());
    }
}
