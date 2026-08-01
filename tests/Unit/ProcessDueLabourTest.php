<?php

namespace Tests\Unit;

use App\Http\Middleware\ProcessDueLabour;
use App\Services\Economy\LabourProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ProcessDueLabourTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        config([
            'economy.process_labour_on_request' => true,
            'economy.labour_request_interval' => 30,
        ]);
    }

    public function test_requests_process_due_labour_at_most_once_per_interval(): void
    {
        $processor = Mockery::mock(LabourProcessor::class);
        $processor->shouldReceive('processDue')->once()->andReturn($this->stats());
        $middleware = new ProcessDueLabour($processor);
        $request = Request::create('/');

        $this->assertSame('ok', $middleware->handle($request, fn () => new Response('ok'))->getContent());
        $this->assertSame('ok', $middleware->handle($request, fn () => new Response('ok'))->getContent());
    }

    private function stats(): array
    {
        return [
            'processed' => 0,
            'finished' => 0,
            'paused' => 0,
            'skipped_resources' => 0,
            'limit_reached' => false,
            'busy' => false,
        ];
    }
}
