<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\View;
use App\Services\PermissionService;

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
        Relation::enforceMorphMap([
            0 => 'App\Models\User',
            1 => 'App\Models\Thread',
            2 => 'App\Models\Company',
            3 => 'App\Models\Board',
            4 => 'App\Models\Group',
            5 => 'App\Models\Encyclopedia',
            6 => 'App\Models\Character',
            8 => 'App\Models\CompanyWorker',
            'character' => 'App\Models\Character',
            'thread' => 'App\Models\Thread',
            'user' => 'App\Models\User',
        ]);

        Blade::if('permission', function ($permission, $object = null, $user = null) {
            $permissionService = app(PermissionService::class);
            $permission = $permissionService->check(strtolower($permission), $object);
            if ($user) {
                if ($permission === 2 || $permission === 1 && $object->userLegacy->id === $user->id) {
                    return true;
                }
            } elseif ($permission > 0) {
                return true;
            }
            return false;
        });

        // Register a view composer for all views
        Facades\View::composer('*', function (View $view) {
            // Share flash messages with the view
            $view->with('flashMessages', session('flash_messages', []));
        });
    }
}
