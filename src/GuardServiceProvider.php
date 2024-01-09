<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard;

use AmdadulHaq\Guard\Middleware\EnsureUserHasRole;
use AmdadulHaq\Guard\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
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
            ->hasMigrations(['create_roles_table', 'create_permissions_table']);
    }

    public function bootingPackage(): void
    {
        app('router')->aliasMiddleware('role', EnsureUserHasRole::class);

        try {
            DB::connection()->getPdo();

            if (DB::connection()->getDatabaseName() && Schema::hasTable('permissions')) {
                foreach ($this->getPermissions() as $permission) {
                    /** @phpstan-ignore-next-line */
                    Gate::define(Str::replaceFirst('-', '.', $permission->slug), function (User $user) use ($permission) {
                        /** @phpstan-ignore-next-line */
                        return $user->hasPermission($permission);
                    });
                }
            }
        } catch (\Exception $e) {
            // throw $e->getMessage();
        }
    }

    protected function getPermissions()
    {
        return Permission::with('roles')->get();
    }
}
