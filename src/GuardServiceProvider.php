<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard;

use AmdadulHaq\Guard\Commands\CreatePermission;
use AmdadulHaq\Guard\Commands\CreateRole;
use AmdadulHaq\Guard\Contracts\Permission as PermissionContract;
use AmdadulHaq\Guard\Contracts\Role as RoleContract;
use AmdadulHaq\Guard\Contracts\User as UserContract;
use AmdadulHaq\Guard\Enums\CacheKey;
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
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/guard.php',
            'guard'
        );

        $this->app->bind(PermissionContract::class, fn () => resolve(config('guard.models.permission')));
        $this->app->bind(RoleContract::class, fn () => resolve(config('guard.models.role')));
    }

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

    protected function publishConfig(): void
    {
        $this->publishes([
            __DIR__.'/../config/guard.php' => config_path('guard.php'),
        ], 'guard-config');
    }

    protected function publishMigrations(): void
    {
        $this->publishes([
            __DIR__.'/../database/migrations/create_roles_table.php.stub' => database_path('migrations/'.date('Y_m_d_His').'_create_roles_table.php'),
            __DIR__.'/../database/migrations/create_permissions_table.php.stub' => database_path('migrations/'.date('Y_m_d_His').'_create_permissions_table.php'),
        ], 'guard-migrations');
    }

    protected function registerCommands(): void
    {
        $this->commands([
            CreateRole::class,
            CreatePermission::class,
        ]);
    }

    protected function registerMiddleware(): void
    {
        $router = $this->app->make(Router::class);

        $router->aliasMiddleware('role', RoleMiddleware::class);
        $router->aliasMiddleware('permission', PermissionMiddleware::class);
        $router->aliasMiddleware('role_or_permission', RoleOrPermissionMiddleware::class);
    }

    protected function registerModelObservers(): void
    {
        foreach ([Role::class, Permission::class] as $model) {
            $model::saved(fn () => $this->clearCache());
            $model::deleted(fn () => $this->clearCache());
        }
    }

    protected function permissionsTableExists(): bool
    {
        try {
            return Schema::hasTable('permissions');
        } catch (Throwable) {
            return false;
        }
    }

    protected function defineGatePermissions(): void
    {
        foreach ($this->getPermissions() as $permission) {
            $permissionName = $permission->getName();
            if (! Gate::has($permissionName)) {
                Gate::define($permissionName, fn (UserContract $user): bool => $user->hasPermissionByName($permissionName));
            }
        }
    }

    protected function defineGateRoles(): void
    {
        foreach ($this->getRoles() as $role) {
            $roleName = $role->getName();
            if (! Gate::has($roleName)) {
                Gate::define($roleName, fn (UserContract $user): bool => $user->hasRole($roleName));
            }
        }
    }

    /**
     * @return Collection<int, Permission>
     */
    protected function getPermissions(): Collection
    {
        $cacheEnabled = config('guard.cache.enabled', true);

        if (! $cacheEnabled) {
            return Permission::with('roles')->get();
        }

        $cacheDuration = config('guard.cache.permissions_duration', 3600);

        return Cache::remember(CacheKey::PERMISSIONS->value, $cacheDuration, fn () => Permission::with('roles')->get());
    }

    /**
     * @return Collection<int, Role>
     */
    protected function getRoles(): Collection
    {
        $cacheEnabled = config('guard.cache.enabled', true);

        if (! $cacheEnabled) {
            return Role::all();
        }

        $cacheDuration = config('guard.cache.roles_duration', 3600);

        return Cache::remember(CacheKey::ROLES->value, $cacheDuration, fn () => Role::all());
    }

    public function clearCache(): void
    {
        Cache::forget(CacheKey::PERMISSIONS->value);
        Cache::forget(CacheKey::ROLES->value);

        if (config('guard.cache.tags', false)) {
            Cache::tags([CacheKey::PERMISSIONS->value, CacheKey::ROLES->value])->flush();
        }
    }

    public static function staticClearCache(): void
    {
        $instance = resolve(self::class);
        $instance->clearCache();
    }
}
