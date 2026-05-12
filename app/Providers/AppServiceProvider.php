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
            1 => 'App\Models\Board\Thread',
            2 => 'App\Models\Company',
            3 => 'App\Models\Board\Board',
            4 => 'App\Models\Group',
            5 => 'App\Models\Page',
            6 => 'App\Models\Character',
            8 => 'App\Models\CompanyWorker',
            'company_worker' => 'App\Models\CompanyWorker',
            'character' => 'App\Models\Character',
            'dictionary_word' => 'App\Models\Dictionary\Word',
            'territory' => 'App\Models\Territory\Territory',
            'thread' => 'App\Models\Board\Thread',
            'user' => 'App\Models\User',
        ]);

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
