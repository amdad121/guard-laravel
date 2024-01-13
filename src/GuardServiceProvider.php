<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard;

use AmdadulHaq\Guard\Models\Permission;
use AmdadulHaq\Guard\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
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
            ->hasMigrations(['create_roles_table', 'create_permissions_table']);
    }

    public function bootingPackage(): ?string
    {
        try {
            DB::connection()->getPdo();

            if (DB::connection()->getDatabaseName() && Schema::hasTable('permissions')) {
                foreach ($this->getPermissions() as $permission) {
                    /** @phpstan-ignore-next-line */
                    Gate::define($permission->name, function (User $user) use ($permission) {
                        /** @phpstan-ignore-next-line */
                        return $user->hasPermission($permission);
                    });
                }

                foreach ($this->getRoles() as $role) {
                    /** @phpstan-ignore-next-line */
                    Gate::define($role->name, function (User $user) use ($role) {
                        /** @phpstan-ignore-next-line */
                        return $user->hasRole($role->name);
                    });
                }
            }
        } catch (Exception $e) {
            // return $e->getMessage();
        }

        return null;
    }

    protected function getPermissions(): Collection
    {
        return Permission::with('roles')->get();
    }

    protected function getRoles(): Collection
    {
        return Role::get();
    }
}
