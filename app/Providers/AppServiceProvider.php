<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades;
use Illuminate\View\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Custom polymorphic types
        Relation::enforceMorphMap([
            'character' => 'App\Models\Character',
            'thread' => 'App\Models\Thread',
            'user' => 'App\Models\User',
        ]);

        // Register a view composer for all views
        Facades\View::composer('*', function (View $view) {
            // Share flash messages with the view
            $view->with('flashMessages', session('flash_messages', []));
        });
    }
}
