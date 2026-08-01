<?php

namespace App\Providers;

use App\Models\Board\Board;
use App\Models\Board\Post;
use App\Models\Board\Thread;
use App\Models\Economy\Company;
use App\Models\Economy\CompanyWorker;
use App\Models\Economy\CompanySite;
use App\Models\Territory\Location;
use App\Policies\Board\BoardPolicy;
use App\Policies\Board\PostPolicy;
use App\Policies\Board\ThreadPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\CompanyWorkerPolicy;
use App\Policies\CompanySitePolicy;
use App\Policies\LocationPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Board::class => BoardPolicy::class,
        Company::class => CompanyPolicy::class,
        CompanySite::class => CompanySitePolicy::class,
        CompanyWorker::class => CompanyWorkerPolicy::class,
        Location::class => LocationPolicy::class,
        Post::class => PostPolicy::class,
        Thread::class => ThreadPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
