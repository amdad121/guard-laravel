<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpgradeCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'guard:upgrade';

    /**
     * The console command description.
     */
    protected $description = 'Upgrade Guard package models from V1 to V2 architecture';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting Guard package upgrade process...');
        $this->newLine();

        $modelsPath = app_path('Models');

        if (! File::isDirectory($modelsPath)) {
            $this->error('App/Models directory not found.');

            return self::FAILURE;
        }

        $files = File::allFiles($modelsPath);
        $upgradedCount = 0;

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $content = File::get($file->getRealPath());
            $originalContent = $content;

            // Update trait import paths from V1 and V2 pre-release to current
            $content = preg_replace(
                '/use\s+AmdadulHaq\\\\Guard\\\\HasRoles\s*;/',
                'use AmdadulHaq\Guard\Concerns\Roleable;',
                $content
            );
            $content = preg_replace('/use\s+AmdadulHaq\\\\Guard\\\\HasPermissions\s*;\r?\n?/', '', $content);

            $content = preg_replace(
                '/use\s+AmdadulHaq\\\\Guard\\\\Concerns\\\\HasRoles\s*;/',
                'use AmdadulHaq\Guard\Concerns\Roleable;',
                $content
            );

            // HasPermissions is merged into Roleable; remove its import and trait use
            $content = preg_replace('/use\s+AmdadulHaq\\\\Guard\\\\Concerns\\\\HasPermissions\s*;\r?\n?/', '', $content);

            $content = preg_replace('/use\s+HasRoles\s*;\r?\n?/', "use Roleable;\n", $content);
            $content = preg_replace('/use\s+HasPermissions\s*;\r?\n?/', '', $content);
            $content = preg_replace('/use\s+HasPermissions\s*,\s*HasRoles\s*;\r?\n?/', "use Roleable;\n", $content);
            $content = preg_replace('/use\s+HasRoles\s*,\s*HasPermissions\s*;\r?\n?/', "use Roleable;\n", $content);

            // Update contract imports
            $content = str_replace(
                'use AmdadulHaq\Guard\Contracts\Roles as RolesContract;',
                'use AmdadulHaq\Guard\Contracts\Roleable as RoleableContract;',
                $content
            );
            $content = str_replace(
                'use AmdadulHaq\Guard\Contracts\Roles;',
                'use AmdadulHaq\Guard\Contracts\Roleable as RoleableContract;',
                $content
            );
            $content = str_replace(
                'use AmdadulHaq\Guard\Contracts\User as UserContract;',
                'use AmdadulHaq\Guard\Contracts\Roleable as RoleableContract;',
                $content
            );
            $content = str_replace(
                'use AmdadulHaq\Guard\Contracts\User;',
                'use AmdadulHaq\Guard\Contracts\Roleable as RoleableContract;',
                $content
            );

            // Permissions contract is no longer used; remove it
            $content = str_replace("use AmdadulHaq\Guard\Contracts\Permissions;\n", '', $content);
            $content = str_replace("use AmdadulHaq\Guard\Contracts\Permissions;", '', $content);

            // De-duplicate RoleableContract import when both User and Roles were originally imported
            $content = preg_replace(
                "/(use AmdadulHaq\\\\Guard\\\\Contracts\\\\Roleable as RoleableContract;\r?\n){2,}/",
                "use AmdadulHaq\\Guard\\Contracts\\Roleable as RoleableContract;\n",
                $content
            );

            // Replace old contract aliases in class declarations
            $content = preg_replace('/\bRolesContract\b/', 'RoleableContract', $content);
            $content = preg_replace('/\bUserContract\b/', 'RoleableContract', $content);

            // Match 'User' and 'Roles' only in implements clauses to avoid replacing unrelated identifiers
            $content = preg_replace('/(?<=implements\s|,\s)\bUser\b(?=\s*,|\s*\{|\s*$)/', 'RoleableContract', $content);
            $content = preg_replace('/(?<=implements\s|,\s)\bRoles\b(?=\s*,|\s*\{|\s*$)/', 'RoleableContract', $content);

            // Collapse duplicate RoleableContract in implements list (e.g. implements User, Roles -> both replaced)
            $content = preg_replace('/RoleableContract\s*,\s*RoleableContract/', 'RoleableContract', $content);

            if ($content !== $originalContent) {
                File::put($file->getRealPath(), $content);
                $this->line("Upgraded: {$file->getFilename()}");
                $upgradedCount++;
            }
        }

        $this->newLine();

        if ($upgradedCount > 0) {
            $this->info("Successfully upgraded {$upgradedCount} models to V2 architecture!");
        } else {
            $this->info("No models required upgrading. You're already up to date.");
        }

        $this->newLine();

        $migrationExists = ! empty(glob(database_path('migrations/*_create_permissions_table.php'))) || ! empty(glob(database_path('migrations/*_create_roles_table.php')));

        if ($migrationExists) {
            $this->info('Existing Guard migrations found. Publishing V2 schema upgrade migrations...');
            $this->call('vendor:publish', [
                '--tag' => 'guard-upgrade-migrations',
            ]);
            $this->info('Upgrade migrations successfully published! Please run `php artisan migrate`.');
        } else {
            $this->info('No existing Guard migrations found. You can publish them using:');
            $this->line('  php artisan vendor:publish --tag="guard-migrations"');
        }

        return self::SUCCESS;
    }
}
