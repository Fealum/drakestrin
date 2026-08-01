<?php

namespace App\Http\Middleware;

use App\Services\Economy\LabourProcessor;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ProcessDueLabour
{
    private const CACHE_KEY = 'economy:labour-request-processing';

    public function __construct(private LabourProcessor $processor) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('economy.process_labour_on_request', true)) {
            return $next($request);
        }

        $interval = max(1, (int) config('economy.labour_request_interval', 30));

        try {
            if (! Cache::add(self::CACHE_KEY, true, $interval)) {
                return $next($request);
            }

            $stats = $this->processor->processDue();

            if ($stats['limit_reached'] || $stats['busy']) {
                Cache::forget(self::CACHE_KEY);
            }
        } catch (Throwable $exception) {
            report($exception);
        }

        return $next($request);
    }
}
