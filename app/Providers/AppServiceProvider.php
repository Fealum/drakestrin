<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\View;
use App\Services\PermissionService;
use App\Support\PermissionEntityType;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PermissionService::class, function ($app) {
            return new PermissionService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Custom polymorphic types
        Relation::enforceMorphMap(PermissionEntityType::morphMap());

        Blade::if('permission', function ($permission, $object = null, $user = null) {
            $permissionService = app(PermissionService::class);
            $permission = strtolower($permission);

            if ($user) {
                $objectUserId = $object->user_id ?? $object->user ?? $object->user?->id ?? null;
                $objectUserId = is_object($objectUserId) ? $objectUserId->id : $objectUserId;

                return $permissionService->allowsOwn($permission, $object, $objectUserId, $user);
            }

            return $permissionService->allows($permission, $object);
        });

        // Register a view composer for all views
        Facades\View::composer('*', function (View $view) {
            // Share flash messages with the view
            $view->with('flashMessages', session('flash_messages', []));
        });
    }
}
