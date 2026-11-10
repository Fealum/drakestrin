<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\RegisterProviders;
use RuntimeException;

trait CreatesApplication
{
    /**
     * Creates the application.
     */
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        if ($app->configurationIsCached()) {
            throw new RuntimeException('Tests refuse cached configuration. Run php artisan config:clear first.');
        }

        $app->beforeBootstrapping(RegisterProviders::class, fn (Application $app) => $this->assertTestingDatabase($app));
        $app->make(Kernel::class)->bootstrap();
        $this->assertTestingDatabase($app);

        return $app;
    }

    private function assertTestingDatabase(Application $app): void
    {
        $connection = $app['config']->get('database.connections.'.$app['config']->get('database.default'));
        if (! $app->environment('testing') || ($connection['host'] ?? null) !== 'db-test'
            || ($connection['database'] ?? null) !== 'drakestrin_testing'
            || ($connection['username'] ?? null) !== 'drakestrin_testing'
            || filled($connection['url'] ?? null) || filled($connection['unix_socket'] ?? null)) {
            throw new RuntimeException('Tests may only connect to db-test/drakestrin_testing using the dedicated test account.');
        }
    }
}
