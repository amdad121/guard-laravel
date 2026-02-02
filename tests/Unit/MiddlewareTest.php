<?php

declare(strict_types=1);

use AmdadulHaq\Guard\Exceptions\PermissionDeniedException;
use AmdadulHaq\Guard\Middleware\PermissionMiddleware;
use AmdadulHaq\Guard\Middleware\RoleMiddleware;
use AmdadulHaq\Guard\Models\Permission;
use AmdadulHaq\Guard\Models\Role;
use AmdadulHaq\Guard\Tests\Models\User;
use Illuminate\Support\Facades\Route;

beforeEach(function (): void {
    $this->user = User::query()->create(['name' => 'Test User', 'email' => 'test@example.com', 'password' => 'password']);
    $this->role = Role::query()->create(['name' => 'admin']);
    $this->permission = Permission::query()->create(['name' => 'users.create']);
});

it('allows access when user has required role via RoleMiddleware', function (): void {
    $this->user->assignRole($this->role);

    Route::middleware([RoleMiddleware::class.':admin'])->get('/test-role', fn (): string => 'success');

    $response = $this->actingAs($this->user)->get('/test-role');

    expect($response->status())->toBe(200)
        ->and($response->content())->toBe('success');
});

it('throws PermissionDeniedException when user lacks required role via RoleMiddleware', function (): void {
    Route::middleware([RoleMiddleware::class.':admin'])->get('/test-role', fn (): string => 'success');

    $this->withoutExceptionHandling()->actingAs($this->user)->get('/test-role');
})->throws(PermissionDeniedException::class);

it('returns 403 when unauthenticated user accesses role protected route via RoleMiddleware', function (): void {
    Route::middleware([RoleMiddleware::class.':admin'])->get('/test-role', fn (): string => 'success');

    $response = $this->get('/test-role');

    expect($response->status())->toBe(403);
});

it('allows access when user has any of multiple roles via RoleMiddleware', function (): void {
    $editorRole = Role::query()->create(['name' => 'editor']);
    $this->user->assignRole($editorRole);

    Route::middleware([RoleMiddleware::class.':admin,editor'])->get('/test-multi-role', fn (): string => 'success');

    $response = $this->actingAs($this->user)->get('/test-multi-role');

    expect($response->status())->toBe(200);
});

it('allows access when user has required permission via PermissionMiddleware', function (): void {
    $this->role->givePermissionTo($this->permission);
    $this->user->assignRole($this->role);

    Route::middleware([PermissionMiddleware::class.':users.create'])->get('/test-permission', fn (): string => 'success');

    $response = $this->actingAs($this->user)->get('/test-permission');

    expect($response->status())->toBe(200)
        ->and($response->content())->toBe('success');
});

it('throws PermissionDeniedException when user lacks required permission via PermissionMiddleware', function (): void {
    Route::middleware([PermissionMiddleware::class.':users.create'])->get('/test-permission', fn (): string => 'success');

    $this->withoutExceptionHandling()->actingAs($this->user)->get('/test-permission');
})->throws(PermissionDeniedException::class);
