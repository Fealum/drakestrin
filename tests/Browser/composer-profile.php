<?php

use App\Http\Middleware\CheckNewMessage;
use App\Http\Middleware\FetchPermissions;
use App\Http\Middleware\ProcessDueLabour;
use App\Http\Middleware\ProcessThreadNotifications;
use App\Http\Middleware\RenderPostComposer;
use App\Http\Middleware\TrackOnline;
use App\Models\Board\PostDraft;
use App\Models\User;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tests\CreatesApplication;

require __DIR__.'/../../vendor/autoload.php';
$bootstrap = new class
{
    use CreatesApplication;
};
$app = $bootstrap->createApplication();
$fixture = json_decode(file_get_contents(storage_path('app/testing/composer-browser.json')), true, flags: JSON_THROW_ON_ERROR);
$measurements = [];
$queries = [];
DB::listen(function ($query) use (&$queries) {
    $queries[] = ['sql' => $query->sql, 'ms' => $query->time];
});
foreach ([ProcessDueLabour::class, ProcessThreadNotifications::class, TrackOnline::class, FetchPermissions::class, CheckNewMessage::class, RenderPostComposer::class] as $class) {
    $app->extend($class, function ($middleware) use ($class, &$measurements, &$queries) {
        return new class($middleware, $class, $measurements, $queries)
        {
            public function __construct(private object $middleware, private string $name, private array &$measurements, private array &$queries) {}

            public function __call(string $name, array $arguments): mixed
            {
                return $this->middleware->$name(...$arguments);
            }

            public function handle($request, $next)
            {
                $started = hrtime(true);
                $before = count($this->queries);
                $downstream = 0;
                $downstreamQueries = 0;
                $response = $this->middleware->handle($request, function ($request) use ($next, &$downstream, &$downstreamQueries) {
                    $started = hrtime(true);
                    $before = count($this->queries);
                    $response = $next($request);
                    $downstream = hrtime(true) - $started;
                    $downstreamQueries = count($this->queries) - $before;

                    return $response;
                });
                $this->measurements[class_basename($this->name)] = [
                    'ms' => round((hrtime(true) - $started - $downstream) / 1e6, 1),
                    'queries' => count($this->queries) - $before - $downstreamQueries,
                ];

                return $response;
            }
        };
    });
}

Auth::login(User::findOrFail($fixture['user']));
foreach (['cold', 'warm', 'action'] as $case) {
    $measurements = [];
    $queries = [];
    $version = PostDraft::where('user_id', $fixture['user'])->where('thread_id', $fixture['thread'])->value('version') ?? 0;
    $request = Request::create('/thread/'.$fixture['thread'].'/draft', 'POST', [
        'character' => $fixture['characters'][0], 'version' => $version, 'intent' => 'save', 'thread_page' => 2,
        'elements' => [['type' => 'message', 'message' => $case === 'action' ? 'Before[action /]After' : 'Profiling draft']],
    ], server: ['HTTP_HX_REQUEST' => 'true', 'HTTP_ACCEPT' => 'text/html']);
    $started = hrtime(true);
    $response = $app->make(Kernel::class)->handle($request);
    $slowest = $queries;
    usort($slowest, fn ($left, $right) => $right['ms'] <=> $left['ms']);
    echo json_encode([
        'case' => $case, 'status' => $response->getStatusCode(),
        'total_ms' => round((hrtime(true) - $started) / 1e6, 1),
        'sql_ms' => round(array_sum(array_column($queries, 'ms')), 1), 'queries' => count($queries),
        'middleware' => $measurements, 'server_timing' => $response->headers->get('Server-Timing'),
        'slowest' => array_slice($slowest, 0, 3),
    ], JSON_PRETTY_PRINT)."\n";
    $app->make(Kernel::class)->terminate($request, $response);
}
