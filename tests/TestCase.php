<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Tests;

use AmdadulHaq\Guard\GuardServiceProvider;
use Illuminate\Database\ConnectionResolverInterface;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            GuardServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        $app->make(ConnectionResolverInterface::class)->connection()->getSchemaBuilder()->create('roles', function ($table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_guarded')->default(false);
            $table->timestamps();
        });

        $app->make(ConnectionResolverInterface::class)->connection()->getSchemaBuilder()->create('permissions', function ($table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->string('group')->nullable();
            $table->boolean('is_wildcard')->default(false);
            $table->timestamps();
        });

        $app->make(ConnectionResolverInterface::class)->connection()->getSchemaBuilder()->create('permission_role', function ($table): void {
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->primary(['permission_id', 'role_id']);
        });

        $app->make(ConnectionResolverInterface::class)->connection()->getSchemaBuilder()->create('role_user', function ($table): void {
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->primary(['role_id', 'user_id']);
        });
    }

    protected function tearDown(): void
    {
        $this->app->make(ConnectionResolverInterface::class)->connection()->getSchemaBuilder()->dropIfExists('role_user');
        $this->app->make(ConnectionResolverInterface::class)->connection()->getSchemaBuilder()->dropIfExists('permission_role');
        $this->app->make(ConnectionResolverInterface::class)->connection()->getSchemaBuilder()->dropIfExists('permissions');
        $this->app->make(ConnectionResolverInterface::class)->connection()->getSchemaBuilder()->dropIfExists('roles');

        parent::tearDown();
    }
}
