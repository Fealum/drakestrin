<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

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
    }
}
