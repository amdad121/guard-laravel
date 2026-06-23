<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Tests;

use AmdadulHaq\Guard\GuardServiceProvider;
use AmdadulHaq\Guard\Tests\Models\User;
use Illuminate\Database\ConnectionResolverInterface;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            GuardServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('guard.models.user', User::class);

        $schema = $app->make(ConnectionResolverInterface::class)->connection()->getSchemaBuilder();

        $schema->create('users', function ($table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        $schema->create('roles', function ($table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_guarded')->default(false);
            $table->timestamps();
        });

        $schema->create('permissions', function ($table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->string('group')->nullable();
            $table->boolean('is_wildcard')->default(false);
            $table->timestamps();
        });

        $schema->create('permission_role', function ($table): void {
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->primary(['permission_id', 'role_id']);
        });

        $schema->create('role_user', function ($table): void {
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->primary(['role_id', 'user_id']);
        });
    }

    protected function tearDown(): void
    {
        $schema = $this->app->make(ConnectionResolverInterface::class)->connection()->getSchemaBuilder();
        $schema->dropIfExists('role_user');
        $schema->dropIfExists('permission_role');
        $schema->dropIfExists('permissions');
        $schema->dropIfExists('roles');
        $schema->dropIfExists('users');

        parent::tearDown();
    }
}
