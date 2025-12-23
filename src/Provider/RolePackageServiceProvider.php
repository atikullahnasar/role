<?php

namespace atikullahnasar\role\Provider;

use atikullahnasar\role\Repositories\Permissions\PermissionRepository;
use atikullahnasar\role\Repositories\Permissions\PermissionRepositoryInterface;
use atikullahnasar\role\Repositories\Roles\RoleRepository;
use atikullahnasar\role\Repositories\Roles\RoleRepositoryInterface;
use atikullahnasar\role\Services\Permissions\PermissionService;
use atikullahnasar\role\Services\Permissions\PermissionServiceInterface;
use atikullahnasar\role\Services\Roles\RoleService;
use atikullahnasar\role\Services\Roles\RoleServiceInterface;
use Illuminate\Support\ServiceProvider;

class RolePackageServiceProvider extends ServiceProvider
{

    public function boot()
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');

        // Publish config
        $this->publishes([
            __DIR__ . '/../Config/role.php' => config_path('role.php'),
        ], 'role-config');

        // Load views based on config
        $layout = config('role.layout', 'bootstrap');
        // dd($layout);
        $this->loadViewsFrom(__DIR__ . '/../Resources/views/' . $layout, 'roles');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../Database/migrations' => database_path('migrations'),
        ], 'role-migrations');

        $this->loadMigrationsFrom(__DIR__ . '/../Database/migrations');
    }

    public function register()
    {
        $this->app->bind(RoleServiceInterface::class, RoleService::class);
        $this->app->bind(PermissionServiceInterface::class, PermissionService::class);

        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
    }
}
